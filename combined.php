<?php
session_start();

$errorMessage = '';

if (isset($_GET['error'])) {
    // Get the error message
    $errorMessage = $_GET['error'];
  }

$label = ["Shirt", "Ball", "Shoe"];

$max = 3;
$i = $j = $r = $flag = 0;
$randnum = array_fill(0, $max, 0);

for ($i = 0; $i < $max; $i++) {
    if ($i == 0) {
        $randnum[0] = mt_rand(1, $max);
    }
    do {
        $r = mt_rand(1, $max);
        $leave = 1;
        for ($j = 0; $j < $i; $j++) {
            if ($r == $randnum[$j])
                $flag++;
        }
        if ($flag == 0) {
            $randnum[$i] = $r;
            $leave = 0;
        }
        $flag = 0;
    } while ($leave != 0);
}

$key = implode("", $randnum);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Drag and Drop Game</title>
  <link rel="stylesheet" href="style_combined2.css">
</head>

<body>

<div class="button-container">
<div class="error-message "><?php echo $errorMessage?></div>
</div>

<div class="container">

  <div class="object-container" id="oc">
    <img class="object" src="shirt.png" id="object1">
    <img class="object" src="ball.png" id="object2">
    <img class="object" src="shoe.png" id="object3">
  </div>

  <div class="area-container1">
    <div class="area1" id="bucket1"></div>
    <div class="text" id="bucket1text"><?php echo $label[$randnum[0] - 1]; ?> bucket</div>
  </div>

  <div class="area-container2">
    <div class="area2" id="bucket2"></div>
    <div class="text" id="bucket2text"><?php echo $label[$randnum[1] - 1]; ?> bucket</div>
  </div>

  <div class="area-container3">
    <div class="area3" id="bucket3"></div>
    <div class="text" id="bucket3text"><?php echo $label[$randnum[2] - 1]; ?> bucket</div>
  </div>

  <div class="logo-container" id="logo">
    <img src="logo.png">
  </div>
</div>

<div class="button-container">
  <!-- Reset button -->
  <input type="button" value="Reset" onclick="location.reload(true)">
  <!-- Submit button -->
  <input type="button" onclick="checkPlacement()" value="Submit">
</div>


<script>
  const objects = document.querySelectorAll('.object');
  const buckets = document.querySelectorAll('.area1, .area2, .area3');

  var objectIdOrder = <?php echo json_encode($randnum); ?>;

  document.addEventListener("DOMContentLoaded", function() {
    const objects = document.querySelectorAll('.object');
    objects.forEach(function(object, index) {
      const objectId = object.id;
      const bucketId = 'bucket' + objectIdOrder[index];
      const bucket = document.getElementById(bucketId);
      if (bucket) {
        bucket.setAttribute('data-target', objectId);
      }
    });
  });

  objects.forEach(object => {
    object.addEventListener('dragstart', dragStart);
  });

  buckets.forEach(bucket => {
    bucket.addEventListener('dragover', dragOver);
    bucket.addEventListener('dragenter', dragEnter);
    bucket.addEventListener('dragleave', dragLeave);
    bucket.addEventListener('drop', drop);
  });

  function dragStart(event) {
    event.dataTransfer.setData('text/plain', event.target.id);
  }

  function dragOver(event) {
    event.preventDefault();
  }

  function dragEnter(event) {
    event.preventDefault();
    const currentBucket = event.target;
    currentBucket.classList.add('highlight');
  }

  function dragLeave(event) {
    const currentBucket = event.target;
    currentBucket.classList.remove('highlight');
  }

  function drop(event) {
    event.preventDefault();
    const droppedObjectID = event.dataTransfer.getData('text/plain');
    const droppedObject = document.getElementById(droppedObjectID);
    const currentBucket = event.target;
    currentBucket.classList.remove('highlight');
    currentBucket.appendChild(droppedObject);
  }

  function checkPlacement() {
    let allCorrect = true;

    buckets.forEach(function(bucket) {
      const dataTarget = bucket.getAttribute('data-target');
      const objectIdInBucket = document.getElementById(dataTarget);
      if (!objectIdInBucket || objectIdInBucket.parentNode !== bucket) {
        console.log(`Wrong or No object is placed in bucket ${bucket.id}`);
        allCorrect = false;
      }
    });

    if (allCorrect) {
      window.location.href = "welcome.php";
    } else {
      //window.location.reload();
      const errorMessage = encodeURIComponent("CAPTCHA solved incorrectly");

      // Refresh the page with the error message as a query parameter
      window.location.href = window.location.href.split('?')[0] + '?error=' + errorMessage;
    }
  }

</script>

</body>
</html>
