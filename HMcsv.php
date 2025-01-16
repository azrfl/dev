<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Duplication</title>
    <link rel="icon" href="asset\pmqcicon.ico" type="image/icon type">
    <style>

.apung{position:absolute;top:1px;right:20px;color:#888;font-size:8px}
body{font-family:Arial,sans-serif;color:#333;padding:0px;}
.hero{color:#1d4289;font-size: 1.7em;font-weight:bold ;padding-top:5px}
h4 span{font-size:12px}

.container{height:fit-content;width:90vw;display:flex;flex-direction:row;gap:10px;padding:10px;border-radius:10px}
.scrollable-box div{max-width:80vw;background:#ffffff;margin:3px 3px; overflow-wrap: break-word;padding:2px 2px}
.scrollable-box{height:45vh;margin-top:5px;width:80vw;overflow-y:auto;font-size:12px;
    border:1px solid #ccc;padding:10px;background-color:#eaeaea;resize:both}
.gallery-section{width:90vw;height:60vh}

input,textarea{width:100%;padding:10px;margin:5px 0;border:1px solid #ccc;border-radius:5px}
button{width:100%;padding:10px;margin:10px 0;background-color:#007bff;color:#fff;border:none;border-radius:5px;cursor:pointer}
button:hover{background-color:#0056b3}

.control-panel{display:flex;flex-direction:column;width:200px}
hr{margin:10px 0}
.result{border:1px solid #5a5a5a;padding:5px}

.fontbutton{width:20px;height:20px;margin:0;padding:0;color:#1d4289;background-color:#ccc}
.error{color:#ff0000}
.scrollable-box div span{color:#1d4289;border:1px solid #ccc;padding:2px 10px;background:#eaeaea}
table{width:100%;border-collapse:collapse}
table,td,th{border:1px solid #bebebe}
#resultGallery td{font-size:12px}
th {background:#eaeaea}
td,th{padding:8px;text-align:left}

.thehead{height:40px;margin:10px;width:cal(100vw - 20px);display:flex;padding:2px;
    border-radius: 5px;
background: #f0f0f0;
box-shadow:  3px 3px 5px #cccccc,
             -3px -3px 5px #ffffff;
}



</style>
</head>
<body>

<div class="thehead">
<img src="asset\pmqcicon.ico" style="height:38px;" alt=""><div class="hero">  &nbsp; &nbsp;QA PMQC : CSV Duplicate Checker</div>
   
</div>

    <div class="container">
     


        <!-- Second Div: Result Gallery -->
        <div class="result-section">
        <h4>Details of CSV  </h4>
    
        <table id="infos" class="infotable">
            <tr><th>Filename</th>           <td id="infofile"></td></tr>
            <tr><th>Total Items</th>        <td id="infototal"></td></tr>
            <tr><th>Unique Items</th>       <td id="infouniq"></td></tr>
            <tr><th>Total Error</th>        <td id="infoerror"></td></tr>        
            <tr><th>Total Duplicate</th>    <td id="infodup"></td></tr>
        </table>

            <br>
            <h4>Duplicate Details &nbsp; &nbsp; &nbsp; &nbsp;



            </h4>
            <div id="resultGallery" ></div>
        </div>

        <!--  <div class="input-section" </div> -->
            
       
        <!-- Fourth Div: Control Panel -->
        <div class="control-panel">
            <button id="uploadBtn">Upload CSV</button>
            <input type="file" id="csvFile" accept=".csv" style="display: none;">
            <hr>

            <h3>Save Result</h3>

            <input type="text" id="poNumber" placeholder="PO Number">
            <input type="text" id="soNumber" placeholder="SO Number">
            <input type="text" id="size" placeholder="Size">
            <input type="text" id="barcode" placeholder="Barcode Type">
            <input type="text" id="inspector" placeholder="Inspected By">
            <button id="exportBtn">Export</button>
        </div>




    </div>



    <div class="gallery-section">
        <h4>CSV Uploaded &nbsp; &nbsp; &nbsp; &nbsp;

        <button class="fontbutton" onclick="adjustFontSize('csvGallery', -1)">-</button>
                <span id="csvGalleryFontSize">12px</span>
                <button class="fontbutton" onclick="adjustFontSize('csvGallery', 1)">+</button>

        </h4>       
            <div id="csvGallery" class="scrollable-box" ></div>
        </div>



    <div class="apung">QA QAPR Azraful<BR>2024</div>




<script>
let csvData = []; // This will store the modified rows

// Upload CSV functionality
document.getElementById('uploadBtn').addEventListener('click', () => {
    document.getElementById('csvFile').click();
});

document.getElementById('csvFile').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const csv = e.target.result;
            document.getElementById('infofile').textContent = file.name; // Set filename
            parseCSV(csv);
        };
        reader.readAsText(file);
    }
});

function parseCSV(csv) {
    const normalizedCsv = csv.replace(/\r\n|\r/g, '\n');
    const lines = normalizedCsv.split("\n").map(line => line.trim());

    const csvGallery = document.getElementById('csvGallery');
    csvGallery.innerHTML = ''; // Clear existing content

    let csvData = []; // Clear csvData to store modified rows
    let errorCount = 0;
    let totalItems = 0;

    // Track duplicates
    let itemCount = {};

    // Iterate through each line
    lines.forEach((row, index) => {
        if (row) { // Ignore empty lines
            let modifiedRow = row; // Keep the row as is without replacing GS
            const div = document.createElement('div'); // Create div for each row

            // Check if the row is "ERROR,,," and apply the error class
            if (modifiedRow === "ERROR") {
                errorCount++; // Increment error count
                div.className = 'error'; // Add 'error' class for "ERROR,,,"
            } else {
                // Track item occurrences in itemCount for non-error rows
                if (itemCount[modifiedRow]) {
                    itemCount[modifiedRow]++;
                } else {
                    itemCount[modifiedRow] = 1;
                }
            }

            // Create a span element for the index (start from 1)
            const span = document.createElement('span');
            span.textContent = index + 1; // Index is 0-based, so add 1
            div.appendChild(span); // Append span to div

            // Append the modified row text content
            const textNode = document.createTextNode(` ${modifiedRow}`);
            div.appendChild(textNode); // Append the row text

            // Add div to the csvGallery
            csvGallery.appendChild(div);

            // Add modified row to csvData array
            csvData.push(modifiedRow);
            totalItems++;
        }
    });

    // Calculate and update duplicate count
    const nonErrorItems = totalItems - errorCount;

    // Calculate duplicate count by checking how many rows appeared more than once
    let duplicateCount = 0;
    Object.values(itemCount).forEach(count => {
        if (count > 1) {
            duplicateCount += count - 1; // Count excess occurrences as duplicates
        }
    });

    const uniqueItemsSet = Object.keys(itemCount).length; // Unique items are the keys in itemCount

    document.getElementById('infototal').textContent = totalItems;
    document.getElementById('infouniq').textContent = uniqueItemsSet;
    document.getElementById('infoerror').textContent = errorCount;
    document.getElementById('infodup').textContent = duplicateCount;

    // Populate resultGallery with duplicate details
    populateDuplicateDetails(itemCount, csvData);
}



function populateDuplicateDetails(itemCount, csvData) {
    const resultGallery = document.getElementById('resultGallery');
    resultGallery.innerHTML = ''; // Clear existing content

    const table = document.createElement('table');
    const headerRow = document.createElement('tr');

    // Add table headers
    const seqHeader = document.createElement('th');
    seqHeader.textContent = 'Seq';
    const valueHeader = document.createElement('th');
    valueHeader.textContent = 'Value';
    const countHeader = document.createElement('th');
    countHeader.textContent = 'Count';
    headerRow.appendChild(seqHeader);
    headerRow.appendChild(valueHeader);
    headerRow.appendChild(countHeader);
    table.appendChild(headerRow);

    // Track all occurrences of each item
    let occurrenceMap = {};

    csvData.forEach((item, index) => {
        if (!occurrenceMap[item]) {
            occurrenceMap[item] = [];
        }
        occurrenceMap[item].push(index + 1); // Store 1-based index for each occurrence
    });

    // Add rows for duplicate items
    Object.keys(itemCount).forEach(item => {
        if (itemCount[item] > 1) {
            const row = document.createElement('tr');

            // Seq column (all occurrence indexes, joined by comma and space)
            const seqCell = document.createElement('td');
            seqCell.textContent = occurrenceMap[item].join(', ');

            // Value column
            const valueCell = document.createElement('td');
            valueCell.textContent = item;

            // Count column
            const countCell = document.createElement('td');
            countCell.textContent = itemCount[item];

            row.appendChild(seqCell);
            row.appendChild(valueCell);
            row.appendChild(countCell);
            table.appendChild(row);
        }
    });

    resultGallery.appendChild(table);
}



        function adjustFontSize(elementId, delta) {
    const element = document.getElementById(elementId);
    const fontSizeSpan = document.getElementById(elementId + 'FontSize');
    
    if (!element || !fontSizeSpan) {
        console.error(`Element with ID ${elementId} or corresponding font size span not found.`);
        return;
    }

    let currentFontSize = parseInt(window.getComputedStyle(element).fontSize);
    currentFontSize += delta;

    // Ensure the font size is within the allowed range
    if (currentFontSize >= 6 && currentFontSize <= 24) {
        element.style.fontSize = currentFontSize + 'px';    // Adjust font size of the target element
        fontSizeSpan.textContent = currentFontSize + 'px';  // Update the displayed font size in the span
    }
};

// Export results
document.getElementById('exportBtn').addEventListener('click', () => {
    const poNumber = document.getElementById('poNumber').value.trim();
    const soNumber = document.getElementById('soNumber').value.trim();
    const sizen = document.getElementById('size').value.trim();
    const barcoden = document.getElementById('barcode').value.trim();
    const inspector = document.getElementById('inspector').value.trim();
    const csvfilename = document.getElementById('infofile').textContent.trim();

    if (!poNumber || !soNumber || !barcoden || !inspector || !sizen) {
        alert('All fields are required!');
        return;
    }

    // Generate the current date and time in dd-mm-yyyy hh:mm format
    const now = new Date();
    const formattedDate = now.toLocaleDateString('en-GB'); // dd-mm-yyyy
    const formattedTime = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }); // hh:mm

    // Const title
    const TITLE = 'CSV Duplication Inspection Result';

    // Extract rows from the table inside the resultGallery div
    const resultText = Array.from(document.querySelectorAll('#resultGallery table tr'))
        .map(tr => Array.from(tr.children).map(td => td.textContent.trim()).join(' | '))
        .join('\n');

    // Build the final text content for the exported file
    const fileContent = `${TITLE}\n\n` +
        `CSV Filename: ${csvfilename}\n` +
        `PO Number: ${poNumber}\n` +
        `SO Number: ${soNumber}\n` +
        `Size: ${sizen}\n` +
        `Barcode Type: ${barcoden}\n\n` +
        `${resultText}\n\n` +
        `Inspect by: ${inspector}\n` +
        `Date/Time: ${formattedDate} ${formattedTime}`;

    // File name generation
    const fileName = `CSV_${poNumber}_${soNumber}_${sizen}_${now.toISOString().replace(/[-:.TZ]/g, '').slice(0, 14)}.txt`;

    // Create the file and trigger download
    const blob = new Blob([fileContent], { type: 'text/plain' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = fileName;
    link.click();
});




    
    </script>

</body>
</html>
