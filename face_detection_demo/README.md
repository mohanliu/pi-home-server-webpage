# A dummy demo for face detection using classical computer vision

## Instructions

- To enable camera on browser, please enable `https`. With unofficial certificates, Safari and Firefox are tested to be fine but not Chrome.
- Create self-assigned certificates:

  `openssl req -x509 -newkey rsa:4096 -nodes -out cert.pem -keyout key.pem -days 365`

- Several ways to enable `https` for flask:
  - Add `app.run(ssl_context=('cert.pem', 'key.pem'))` in flask app.py.
  - Run `flask run --cert=cert.pem --key=key.pem`
  - Using Gunicorn by: `gunicorn --certfile cert.pem --keyfile key.pem -b 0.0.0.0:8000 face-detect:app`
  - Setup NGINX by [steps](https://blog.miguelgrinberg.com/post/running-your-flask-application-over-https)

## Sources
> This demo is inspired by following sources:

- Choosing cameras in JavaScript with the mediaDevices API: [blog](https://www.twilio.com/blog/2018/04/choosing-cameras-javascript-mediadevices-api.html), [repo](https://github.com/philnash/mediadevices-camera-selection)
- Launch Your Own Face Recognition Application(Real-Time)In Browser Within Minutes: [blog](https://towardsdatascience.com/launch-your-own-real-time-face-recognition-algorithm-in-your-browser-in-minutes-beginner-guide-a8f2e6fd505c), [repo](https://github.com/BillyFnh/Node.js-Facial-Recognition)
- Build Real Time Face Detection With JavaScript: [youtube](https://youtu.be/CVClHLwv-4I), [repo](https://github.com/WebDevSimplified/Face-Detection-JavaScript)
- Official [face-api.js](https://github.com/justadudewhohacks/face-api.js)
