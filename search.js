"use strict";

function simpleSearch() {
    //parse every row in tables and displays only the ones that match (partially) the search
    //both the search term and the cell content are set to lowercase and accents are removed to improve ease of search
    let rows = document.getElementsByTagName('tr');
    for (let row of rows) {
        const cells = row.getElementsByTagName('td');
        let present = false;
        for (const cell of cells) {
            present = present || matchSearch(cell.innerHTML);
        }
        row.style.display = present ? '' : 'none';
    }
    filterMap()
}

function filterMap() {
    let searchString = cleanString(document.getElementById('search-bar').value);
    let map = document.getElementById("mapID");
    console.log(map);
    console.log(marker)
}

function matchSearch(content) {
    let searchString = cleanString(document.getElementById('search-bar').value);
    let content_clean = cleanString(content);
    console.log(searchString, "->", content_clean);
    return content_clean.indexOf(searchString) > -1;
}

function cleanString(dirtyString) {
    return dirtyString.toLowerCase().normalize('NFKD').replace(/[^\w]/g, '');
}