<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Online Transfer Learning</title>

    <!-- Load the latest version of TensorFlow.js -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/knn-classifier"></script>

    <!-- Load bootstrap for formatting -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"  crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <style type="text/css">
      html,
      body {
        margin: 0;
        padding: 0;
      }

      html {
        height: 100%;
      }

      body {
        font-family: Helvetica, Arial, sans-serif;
        min-height: 100%;
        display: grid;
        grid-template-rows: 1fr auto;
      }

      header {
        background: #f0293e;
        color: #fff;
        text-align: center;
      }
      main {
        background: #ffffff;
        min-height: 60vh;
      }

      .controls {
        text-align: center;
        padding: 0.5em 0;
        background: #333e5a;
      }

      video {
        width: 100%;
        max-width: 600px;
        display: block;
        margin: 0 auto;
      }

      .headercontent {
        text-align: center;
        background: #03DAC6;
        padding: 1.5em 0;
      }
    </style>
  </head>
  <body>


    <!-- Add an image that we will use to test -->
    <!-- <img id="img" crossorigin src="https://i.imgur.com/JlUvsxa.jpg" width="227" height="227"/> -->
    <div class="headercontent row justify-content-md-center" style="text-align: center">
      <h1>Webcam Face Detection</h1>
    </div>
    <div class="headercontent row justify-content-md-center">
      <div class="col-md-4">
        <div class="input-group mb-12">
        <div class="input-group-prepend">
          <label class="input-group-text" for="selectcamera">Camera</label>
        </div>
        <select class="custom-select" id="selectcamera">
        </select>
        </div>
      </div>

      <div class="col-md-4">
        <button id="button" class="btn btn-dark" style="width: 45%;border-radius: 20px;margin: 5px">Start</button>
        <button id="stopbutton" class="btn btn-dark" style="width: 45%;border-radius: 20px;margin: 5px">Stop</button>
      </div>
    </div>

    <main>
      <video autoplay playsinline muted id="webcam"></video>
    </main>
    

    <button id="class-a">Add A</button>
    <button id="class-b">Add B</button>
    <button id="class-c">Add C</button>

    <div id="console">
      prediction: None <br>
      probability: 0.0
    </div>


    <!-- Load index.js after the content of the page -->
    <script type="text/javascript">
      const button = document.getElementById('button');
      const select = document.getElementById('selectcamera');
      let currentStream;


      const classifier = knnClassifier.create();
      const webcamElement = document.getElementById('webcam');
      let net;
      let modelstr;


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
        navigator.mediaDevices
          .getUserMedia(constraints)
          .then(stream => {
            currentStream = stream;
            webcamElement.srcObject = stream;
            return navigator.mediaDevices.enumerateDevices();
          })
          .then(gotDevices)
          .catch(error => {
            console.error(error);
          });
      });

      async function app() {
        console.log('Loading mobilenet..');

        // Load the model.
        net = await mobilenet.load();
        console.log('Successfully loaded model');

        // Create an object from Tensorflow.js data API which could capture image 
        // from the web camera as Tensor.
        const webcam = await tf.data.webcam(webcamElement);

        // Reads an image from the webcam and associates it with a specific class
        // index.
        const addExample = async classId => {
          // Capture an image from the web camera.
          const img = await webcam.capture();

          // Get the intermediate activation of MobileNet 'conv_preds' and pass that
          // to the KNN classifier.
          const activation = net.infer(img, true);

          // Pass the intermediate activation to the classifier.
          classifier.addExample(activation, classId);

          // prepare dataset and save
          modelstr = JSON.stringify( 
            Object.entries(
              classifier.getClassifierDataset()).map(([label, data]) => 
                [label, Array.from(data.dataSync()), data.shape]
              ) 
          );

          // Dispose the tensor to release the memory.
          img.dispose();
        };

        // When clicking a button, add an example for that class.
        document.getElementById('class-a').addEventListener('click', () => addExample(0));
        document.getElementById('class-b').addEventListener('click', () => addExample(1));
        document.getElementById('class-c').addEventListener('click', () => addExample(2));

        while (true) {
          if (classifier.getNumClasses() > 0) {
            const img = await webcam.capture();

            // Get the activation from mobilenet from the webcam.
            const activation = net.infer(img, 'conv_preds');
            // Get the most likely class and confidence from the classifier module.
            const result = await classifier.predictClass(activation);

            const classes = ['A', 'B', 'C'];
            document.getElementById('console').innerHTML = `
              prediction: ${classes[result.label]}<br>
              probability: ${result.confidences[result.label]}
            `;

            // Dispose the tensor to release the memory.
            img.dispose();
          }

          await tf.nextFrame();
        }
        
      }

      app();
      navigator.mediaDevices.enumerateDevices().then(gotDevices);
    </script>
  </body>
</html>
