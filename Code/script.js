$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    var actions = $("table td:last-child").html();
    // Append table with add row form on add new button click
    $(".add-new").click(function() {
        $(this).attr("disabled", "disabled");
        var index = $("table tbody tr:last-child").index();
        var row = '<tr>' +
            '<td><input type="text" class="form-control" name="first-name" id="first-name"></td>' +
            '<td><input type="text" class="form-control" name="last-name" id="last-name"></td>' +
            '<td><select id="promos" name="promos"><option value="CITISE1">CITISE1</option><option value="CITISE2">CITISE2</option>' +
            '<option value="FISE1">FISE1</option><option value="FISE2">FISE2</option><option value="FISE3" selected>FISE3</option> </select>' +
            //'<input type="text" class="form-control" name="promotion" id="promotion"></td>' +
            '<td><input type="text" class="form-control" name="country" id="country"></td>' +
            '<td><input type="text" class="form-control" name="city" id="city"></td>' +
            '<td><input type="text" class="form-control" name="start-date" id="start-date"></td>' +
            '<td><input type="text" class="form-control" name="end-date" id="end-date"></td>' +
            '<td>' + actions + '</td>' +
            '</tr>';
        $("table").append(row);
        $("table tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();
    });
    // Add row on add button click
    $(document).on("click", ".add", function() {
        var empty = false;
        var input = $(this).parents("tr").find('input[type="text"]');
        input.each(function() {
            if (!$(this).val()) {
                $(this).addClass("error");
                empty = true;
            } else {
                $(this).removeClass("error");
            }
        });
        $(this).parents("tr").find(".error").first().focus();
        if (!empty) {
            input.each(function() {
                $(this).parent("td").html($(this).val());
            });
            $(this).parents("tr").find(".add, .edit").toggle();
            $(".add-new").removeAttr("disabled");
        }
        var empty = false;
        var sel = $(this).parents("tr").find('select');
        sel.each(function() {
            if (!$(this).val()) {
                $(this).addClass("error");
                empty = true;
            } else {
                $(this).removeClass("error");
            }
        });
        $(this).parents("tr").find(".error").first().focus();
        if (!empty) {
            sel.each(function() {
                $(this).parent("td").html($(this).val());
            });
        }
    });
    // Edit row on edit button click
    // $(document).on("click", ".edit", function(){		
    //     $(this).parents("tr").find("td:not(:last-child)").each(function(){
    // 		$(this).html('<input type="text" class="form-control" value="' + $(this).text() + '">');
    // 	});		
    // 	$(this).parents("tr").find(".add, .edit").toggle();
    // 	$(".add-new").attr("disabled", "disabled");
    // });
    // Delete row on delete button click
    // $(document).on("click", ".delete", function() {
    //     $(this).parents("tr").remove();
    //     $(".add-new").removeAttr("disabled");
    // });
});