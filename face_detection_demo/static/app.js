const video = document.getElementById('video');
const button = document.getElementById('button');
const select = document.getElementById('selectcamera');
const stopbut = document.getElementById('stopbutton');
let currentStream;
let predictedAges = [];

function interpolateAgePredictions(age) {
  predictedAges = [age].concat(predictedAges).slice(0, 30)
  const avgPredictedAge = predictedAges.reduce((total, a) => total + a) / predictedAges.length
  return avgPredictedAge
}

function roundoutput(num, prec=2) {
  const f = Math.pow(10, prec)
  return Math.floor(num * f) / f
}

function stopMediaTracks(stream) {
  stream.getTracks().forEach(track => {
    track.stop();
  });
}

function gotDevices(mediaDevices) {
  select.innerHTML = '';
  select.appendChild(document.createElement('option'));
  let count = 1;
  mediaDevices.forEach(mediaDevice => {
    if (mediaDevice.kind === 'videoinput') {
      const option = document.createElement('option');
      option.value = mediaDevice.deviceId;
      const label = mediaDevice.label || `Camera ${count++}`;
      const textNode = document.createTextNode(label);
      option.appendChild(textNode);
      select.appendChild(option);
    }
  });
}

button.addEventListener('click', event => {
  if (typeof currentStream !== 'undefined') {
    stopMediaTracks(currentStream);
  }
  const videoConstraints = {};
  if (select.value === '') {
    videoConstraints.facingMode = 'environment';
  } else {
    videoConstraints.deviceId = { exact: select.value };
  }
  const constraints = {
    video: videoConstraints,
    audio: false
  };

  Promise.all([
      faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
      faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
      faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
      faceapi.nets.faceExpressionNet.loadFromUri('/models'),
      faceapi.nets.ageGenderNet.loadFromUri('/models')
  ]).then(function startvideo() {
      navigator.mediaDevices
        .getUserMedia(constraints)
        .then(stream => {
          currentStream = stream;
          video.srcObject = stream;
          return navigator.mediaDevices.enumerateDevices();
        })
        .then(gotDevices)
        .catch(error => {
          console.error(error);
        });
  });
});

stopbut.addEventListener('click', event => {
  if (typeof currentStream !== 'undefined') {
    stopMediaTracks(currentStream);
  }
});


navigator.mediaDevices.enumerateDevices().then(gotDevices);

video.addEventListener('play', () => {
    const canvas = document.getElementById("overlay")

    const dims = faceapi.matchDimensions(canvas, video, true)

    setInterval(async () => {
        if ( document.getElementById("ageandgender").checked ) {
          const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceExpressions().withAgeAndGender()

          const resizedDetections = faceapi.resizeResults(detections, dims)
          canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
          if (document.getElementById("facedetection").checked == true ){
            faceapi.draw.drawDetections(canvas, resizedDetections)
          }
          if (document.getElementById("facelandmarks").checked == true ){
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections)
          }
          if (document.getElementById("faceexpression").checked == true ){
            faceapi.draw.drawFaceExpressions(canvas, resizedDetections)
          }

          const { age, gender, genderProbability } = resizedDetections

          // interpolate gender predictions over last 30 frames
          // to make the displayed age more stable
          const interpolatedAge = interpolateAgePredictions(age)
          new faceapi.draw.DrawTextField(
            [
              `${roundoutput(interpolatedAge, 0)} years`,
              `${gender} (${roundoutput(genderProbability)})`
            ],
            detections.detection.box.topRight
          ).draw(canvas)
        } else {
          const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceExpressions()

          const resizedDetections = faceapi.resizeResults(detections, dims)
          canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
          if (document.getElementById("facedetection").checked == true ){
            faceapi.draw.drawDetections(canvas, resizedDetections)
          }
          if (document.getElementById("facelandmarks").checked == true ){
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections)
          }
          if (document.getElementById("faceexpression").checked == true ){
            faceapi.draw.drawFaceExpressions(canvas, resizedDetections)
          }
        }

    }, 100)
})

