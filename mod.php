<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <img src="./image.php" alt="wykres" id="chart" usemap="#chartmap">
    <map name="chartmap" id="chartmap"></map>
    <!-- <a href="https://www.w3schools.com/tags/att_area_shape.asp">https://www.w3schools.com/tags/att_area_shape.asp</a> -->
    <script>
    const chart = document.getElementById('chartmap');

    async function pobierzDane() {
        const response = await fetch('data.php');
        const data = await response.json();
        // console.log(data);
        data.forEach(element => {
            const area = document.createElement("area");

            area.shape = "circle";
            area.coords = `${element.x},${element.y},10`;
            area.href = `coffee.html?id=${element.id}`;
            area.alt = "Cup of coffee";

            chart.appendChild(area);
            console.log(element.x);
            console.log(element.y);
        });
    }
    pobierzDane();
    </script>
</body>
</html>
todo 
zrob z tego phpw sensie przeniesienie getow z tego do