from flask import Flask, Response, render_template
import cv2

app = Flask(__name__)

@app.route('/')
def index():
    return render_template("index.html")

if __name__ == '__main__':
    app.run(debug=True, port=8080, host='0.0.0.0', ssl_context=('cert.pem', 'key.pem'))
