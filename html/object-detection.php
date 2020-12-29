<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Multiple object detection</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Import the webpage's stylesheet -->
    <link rel="stylesheet" href="static/mod-style.css">
  </head>  
  <body style="background-color:black">
    <h1> Only Support Landscape Mode </h1>
    <section id="demos" class="invisible">
      <div id="liveView" class="camView">
        <button id="webcamButton">Enable Webcam</button>
        <select id="select">
          <option></option>
        </select>
        <video id="webcam" autoplay width="640" height="480" playsinline></video>
      </div>
    </section>

    <!-- Import TensorFlow.js library -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs/dist/tf.min.js" type="text/javascript"></script>
    <!-- Load the coco-ssd model to use to recognize things in images -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd"></script>
    
    <!-- Import the page's JavaScript to do some stuff -->
    <script src="static/mod-script.js" defer></script>
  </body>
</html>
