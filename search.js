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
}

function matchSearch(content) {
    let searchString = cleanString(document.getElementById('search-bar').value);
    let content_clean = cleanString(content);
    return content_clean.indexOf(searchString) > -1;
}

function matchSearchItem(content, searchbarName) {
    const searchTerm = document.getElementById(searchbarName).value;
    if (searchTerm && content) {
        return content.indexOf(cleanString(searchTerm)) > -1;
    } else {
        return true;
    }
}

function matchDates(start, stop, searchbarName) {
    const objective = Date.parse(document.getElementById(searchbarName).value);
    console.log("dates [", start, stop, "] <-", objective);
    console.log(start, "=>", Date.parse(start));
    if (objective) {
        return Date.parse(start) <= objective && Date.parse(stop) >= objective;
    } else {
        return true
    }
}

function cleanString(dirtyString) {
    return dirtyString.toLowerCase().normalize('NFKD').replace(/[^\w]/g, '');
}

function complexSearchMobility() {
    table = document.getElementById('body-mobilities');
    for (let row of table.rows) {
        // information on the left-side of the row
        const leftSide = row.cells[0];
        const student = cleanString(leftSide.children[0].innerText);
        const promotion = cleanString(leftSide.children[1].innerText);

        // information on the right-side of the row
        const rightSide = row.cells[1];
        const location = rightSide.children[0].innerText.split(',');
        const country = cleanString(location[0]);
        const city = cleanString(location[1]);
        const dates = rightSide.children[1].innerText.split(' / ');

        //check validity of the row, the place search tries to match the city or the country
        let valid = matchSearchItem(student, "search-mobility-student") &&
            matchSearchItem(promotion, "search-mobility-promotion") &&
            (matchSearchItem(country, "search-mobility-place") ||
                matchSearchItem(city, "search-mobility-place")) &&
            matchDates(dates[0], dates[1], "search-mobility-date");

        row.style.display = valid ? '' : 'none';
    }
}