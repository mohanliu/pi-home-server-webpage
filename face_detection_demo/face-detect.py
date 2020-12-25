from flask import Flask, Response, render_template, send_from_directory
import cv2

app = Flask(__name__)

@app.route('/')
def index():
    return render_template("index.html")

@app.route('/models/<path:filename>')
def model_static(filename):
    return send_from_directory(app.root_path + '/models/', filename)

if __name__ == '__main__':
    app.run(debug=True, port=3100, host='0.0.0.0', ssl_context=('cert.pem', 'key.pem'))
