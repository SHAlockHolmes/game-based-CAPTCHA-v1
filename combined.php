<?php
// session id
session_start();

//retrive winning bidder's CAPTCHA details
//logo, object[n], label[n]
$label = ["Shirt", "Ball", "Shoe"];

//generate key value

$max=3; //change to value that comes from backend

$i = $j = $r = $flag = 0;

$randnum = array_fill(0, $max, 0);

for ($i=0; $i<$max; $i++)
{
  if ($i==0)
    {$randnum[0] = mt_rand(1, $max);}
  do{
    $r = mt_rand(1, $max);
    $leave =1;
    for ($j=0; $j<$i; $j++)
    {
      if ($r == $randnum[$j])
        $flag++;
    }
    if ($flag==0)
    {
      $randnum[$i] = $r;
      $leave =0 ;
    }
    $flag =0;
  } while ($leave!=0);
}

$key = implode("", $randnum);

//echo $key;

//allocate spaces to everything in the CAPTCHA

// validate solution

//accept by redirecting to the final link or refresh CAPTCHA
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

<div class="container">

  <div class="object-container" id="oc">
    <img class="object" src="shirt.png" id="object1">
    <img class="object" src="ball.png" id="object2">
    <img class="object" src="shoe.png" id="object3">
  </div>

  <div class="area-container1">
    <div class="area1" id="bucket1"></div>
    <div class="text" id="bucket1text"><?php echo $label[$randnum[0]-1];?> bucket</div>
  </div>

  <div class="area-container2">
    <div class="area2" id="bucket2"></div>
    <div class="text" id="bucket1text"><?php echo $label[$randnum[1]-1];?> bucket</div>
  </div>

  <div class="area-container3">
    <div class="area3" id="bucket3"></div>
    <div class="text" id="bucket1text"><?php echo $label[$randnum[2]-1];?> bucket</div>
  </div>

  <div class="logo-container" id="logo">
    <img src="logo.png">
  </div>    
</div>

<div class="button-container">
  <!-- Reset button -->
  <input type="button" value="Reset" onclick="location.reload(true)">
  <!-- Submit button -->
  <input type="submit" onclick="checkPlacement()" value="Submit">
  <!---add reset and submit button--->
</div>



<script>
  const objects = document.querySelectorAll('.object');
  const buckets = document.querySelectorAll('.area1, .area2, .area3');

  
  var objectIdOrder = <?php echo json_encode($randnum); ?>;

  document.addEventListener("DOMContentLoaded", function() {
    // Select all object elements with the class 'object'
    const objects = document.querySelectorAll('.object');
    
    // Loop through each object
    objects.forEach(function(object, index) {
        // Get the object's ID
        const objectId = object.id;
        
        // Find the corresponding bucket element based on the order in objectIdOrder
        const bucketId = 'bucket' + objectIdOrder[index];
        const bucket = document.getElementById(bucketId);
        
        // Assign the object's ID as the value of the 'data-target' attribute for the bucket
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
    // Loop through each bucket
    buckets.forEach(function(bucket) {
        // Get the data-target attribute value of the current bucket
        const dataTarget = bucket.getAttribute('data-target');
        
        // Get the object element with the matching data-target ID
        const objectIdInBucket = document.getElementById(dataTarget);
        
        // Check if the object ID matches the data-target attribute
        if (objectIdInBucket && objectIdInBucket.parentNode === bucket) {
            console.log(`Object ${dataTarget} is correctly placed in bucket ${bucket.id}`);
            // You can perform further actions here if needed
        } else {
            console.log(`Wrong or No object is placed in bucket ${bucket.id}`);
            // You can perform further actions here if needed
        }
    });
}


  function resetPlacement() {
    const incorrectObjects = document.querySelectorAll('.object');
    incorrectObjects.forEach(object => {
      const originalPosition = document.getElementById(object.id + '_original');
      originalPosition.appendChild(object);
    });
  }
</script>

</body>
</html>
