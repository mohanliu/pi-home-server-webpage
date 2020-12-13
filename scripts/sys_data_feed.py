#! /usr/bin/python

"""
This script will save raspberry pi data into MySQL database.

Author: Mohan Liu
Created at: Dec 12, 2020
"""

import os
import sys
import re
import time
import subprocess
import mysql.connector
from datetime import datetime, timedelta

class SystemDataRecording():
    def __init__(self):
        # now time
        self._now_datetime = datetime.now()
 
        # oldest time to keep record
        self._last_checkpoint = self._now_datetime - timedelta(days=7)
        
        # connect mysql database
        self._connect_mysql()

    def _connect_mysql(self):
        """build connection to mysql database"""

        user_name = os.getenv("mysql_username")
        password = os.getenv("mysql_password")
        database = os.getenv("mysql_db")
        host = os.getenv("mysql_host", "127.0.0.1")

        db = mysql.connector.connect(
            user=user_name, 
            password=password,
            host=host,
            database=database
        )

        self._db = db

    def _extract_temperature(self):
        """extract temp from system command"""

        _process = subprocess.Popen(
                ['vcgencmd', 'measure_temp'],
                stdout=subprocess.PIPE, 
                stderr=subprocess.PIPE,
                universal_newlines=True
        )

        stdout, _ = _process.communicate()

        return stdout.strip().replace("temp=", "").replace("'C", "")

    def _feed_temperature(self):
        """feed temperature data into database
        
        Original database creation commnds in MySQL:
        
        MySQL> CREATE TABLE pitemp (created_at Datetime, temperature FLOAT);
        
        """

        # get temperature
        temp = self._extract_temperature()

        # sql insertion command
        insert_cmd_ = "INSERT INTO pitemp values('{}', {})".format(self._now_datetime, temp)
        delete_cmd_ = "DELETE FROM pitemp where created_at < '{}'".format(self._last_checkpoint)

        # start executation
        cursor = self._db.cursor()

        try:
            # Write to the database
            cursor.execute(insert_cmd_)
            cursor.execute(delete_cmd_)

            # Commit the changes
            self._db.commit()
        except Exception as e:
            print("Error is happening, rolling back")
            print(e)
            
            # Roll the database back to the last good setting
            self._db.rollback()
           
    def main(self):
        """main function"""

        self._feed_temperature()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, traceback):
        if hasattr(self, "_db"):
            self._db.close()

if __name__ == "__main__":
    with SystemDataRecording() as s:
        s.main()
