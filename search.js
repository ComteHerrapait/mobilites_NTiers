function simpleSearch() {
    //parse every row in tables and displays only the ones that match (partially) the search
    //both the search term and the cell content are set to lowercase and accents are removed to improve ease of search

    let rows = document.getElementsByTagName('tr');
    let searchString = document.getElementById('search-bar').value.toLowerCase().normalize('NFKD').replace(/[^\w]/g, '');
    for (let i_row = 1; i_row < rows.length; i_row++) {
        let cell = rows[i_row].getElementsByTagName('td');
        let present = 0;
        for (let i_col = 0; i_col < cell.length - 2; i_col++) {
            let cell_content = cell[i_col].innerHTML.toLowerCase().normalize('NFKD').replace(/[^\w]/g, '');
            if (cell_content.indexOf(searchString) > -1) {
                present = 1;
            }
        }
        if (present == 1) {
            rows[i_row].style.display = '';
        } else {
            rows[i_row].style.display = 'none';
        }
    }
}