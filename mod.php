<?php
$width = isset($_GET["width"]) && $_GET["width"] > 0 ? (int)$_GET["width"] : 1000;
$height = isset($_GET["height"]) && $_GET["height"] > 0 ? (int)$_GET["height"] : 400;
$margin = isset($_GET["margin"]) && $_GET["margin"] > 0 ? (int)$_GET["margin"] : 30;
$dayCount = isset($_GET["dayCount"]) && $_GET["dayCount"] > 0 ? (int)$_GET["dayCount"] : 20;
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        area {
            cursor: pointer;
        }

        /* #dialog{
            display: flex;
            flex-direction: column;
            gap: 10px;
        } */
        .form-in-dialog {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>

<body>
    <!-- dialog -->
    <dialog id="dialog">
        <div class="form-in-dialog">
            <input type="number" id="temperatureInput" placeholder="Wprowadź temperaturę">
            <button id="saveTemperature">Zapisz Temperaturę</button>
            <button id="ill">Choroba</button>
            <button id="nothing">Brak pomiaru</button>
            <button commandfor="dialog" command="close">Close</button>
        </div>
    </dialog>



    <!-- dialog -->
    <img alt="wykres" id="chart" usemap="#chartmap">
    <map name="chartmap" id="chartmap"></map>
    <!-- <a href="https://www.w3schools.com/tags/att_area_shape.asp">https://www.w3schools.com/tags/att_area_shape.asp</a> -->
    <script>
        const chart = document.getElementById('chartmap');

        const width = "<?php echo addslashes($width); ?>"
        const height = "<?php echo addslashes($height); ?>"
        const margin = "<?php echo addslashes($margin); ?>"
        const dayCount = "<?php echo addslashes($dayCount); ?>"
        //moze do wyjebana
        let temperaturePoints = [];
        let currentEditedPoint = null;

        const saveTemperatureBtn = document.getElementById('saveTemperature');
        const illBtn = document.getElementById('ill');
        const nothingBtn = document.getElementById('nothing');

        async function fetchData() {
            const response = await fetch(`data.php?width=${width}&height=${height}&margin=${margin}&dayCount=${dayCount}`);
            const data = await response.json();
            temperaturePoints = data;
            // console.log(data);
            data.forEach(element => {
                const area = document.createElement("area");

                area.shape = "circle";
                area.coords = `${element.x},${element.y},10`;

                //lub query selector all i foreach i jak po tablicy :))
                area.dataset.idArea = element.dayNumber;
                area.dataset.temperature = element.temperature > 0 ? element.temperature : " ";

                //to jest testowo
                // area.href = `coffee.html?id=${element.id}`;
                // area.alt = "Cup of coffee";
                area.addEventListener('click', (event) => {
                    currentEditedPoint = event.target.dataset.idArea;
                    // alert(`Clicked on area with ID: ${id}`);
                    document.getElementById('dialog').showModal();
                    document.getElementById('temperatureInput').value = event.target.dataset.temperature;
                });
                chart.appendChild(area);
                console.log(element.x);
                console.log(element.y);
            });
        }
        fetchData();

        async function fetchImage() {
            try {
                const response = await fetch(`./image.php?width=${width}&height=${height}&margin=${margin}&dayCount=${dayCount}`);
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);

                document.getElementById('chart').src = url;
            } catch (error) {
                console.error('Błąd:', error);
            }
        }

        // pierwsze wczytanie
        fetchImage();


        async function editChart(params) {
            try {
                const response = await fetch('editChart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(params)
                });

                const result = await response.json();

                if (result.success) {
                    console.log("Zapis dokonany");
                    fetchData();
                    fetchImage();
                } else {
                    console.log(result.error);
                    // message.textContent = "Błąd: " + result.error;
                }
            } catch (error) {
                // message.textContent = "Błąd sieci: " + error;
                console.log(error);
            }
        }
        saveTemperatureBtn.addEventListener('click', () => {
            const temperature = document.getElementById('temperatureInput').value;
            console.log(`Zapisano temperaturę: ${temperature}, ${currentEditedPoint}`);
            editChart({ temperature: temperature, dayNumber: currentEditedPoint });
            document.getElementById('dialog').close();

        });
        illBtn.addEventListener('click', () => {
            console.log('Zaznaczono chorobę');
            editChart({ illness: true, dayNumber: currentEditedPoint, temperature: null, noMeasurement: null });
            document.getElementById('dialog').close();
        });
        nothingBtn.addEventListener('click', () => {
            console.log('Zaznaczono brak pomiaru');
            editChart({ noMeasurement: true, dayNumber: currentEditedPoint, temperature: null, illness: null });
            document.getElementById('dialog').close();
        });

        //dialog
    </script>
</body>

</html>