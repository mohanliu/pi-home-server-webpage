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
