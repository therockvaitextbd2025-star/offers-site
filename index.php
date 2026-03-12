<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Axiron Tasks</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<h2>Available Tasks</h2>

<div id="loader">⏳ loding offars...</div>

<div id="offers"></div>

<script>

const userId = "<?php echo htmlspecialchars($_GET['user_id'] ?? '', ENT_QUOTES); ?>";

fetch(`api.php?user_id=${userId}`)

.then(res => res.json())

.then(data => {

document.getElementById("loader").style.display = "none";

if(!data.offers || data.offers.length === 0){

document.getElementById("offers").innerHTML =
"<p style='text-align:center;'>No offers right now।</p>";

return;

}

let html="";

data.offers.forEach(o => {

html += `
<div class="offer">

<img src="${o.image}" onerror="this.style.display='none'">

<div class="offer-info">

<h3>${o.title}</h3>

<p>Reward: $${o.payout}</p>

</div>

<a href="${o.link}" target="_blank">Start</a>

</div>
`;

});

document.getElementById("offers").innerHTML = html;

})

.catch(() => {

document.getElementById("loader").innerHTML =
"⚠️ অফার লোড করা যাচ্ছে না";

});

</script>

</body>
</html>
