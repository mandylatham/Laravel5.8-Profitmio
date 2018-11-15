$(document).ready(function() {
    $(".delete-all-missing-field").click(function() {
        $.post("{{ secure_url('campaign/' . $campaign->campaign_id . '/recipients/deletePartialByField/') }}/", {field: $(this).data("field")}, 'json');
        location.reload();
    });

    $(".hide").hide();
    var recipient_data = [];
    var mrBungle = "";

    $.get("{{ secure_url('campaign/' . $campaign->campaign_id . '/recipient-list') }}",
        function(data, recipient_data) {
            recipient_data = data;
        }, 'json');

    $("#recipient-grid").jsGrid({
        width: "100%",
        selecting: true,

        filtering: true,
        editing: true,
        inserting: true,
        sorting: true,

        autoload: true,
        paging: true,
        pageSize: 15,
        pageLoading: true,
        pageButtonCount: 10,

        noDataContent: "None found",
        loadIndication: true,
        loadShading: true,

        invalidNotify: function(args) {
            var messages = $.map(args.errors, function(error) {
                return error.field + ": " + error.message;
            });

            console.log(messages);
        },

        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    url: "{{ secure_url('campaign/' . $campaign->campaign_id . '/recipient-list') }}",
                    dataType: "json",
                    data: filter
                });
            },
            insertItem: function(item) {
                item._token = '{{ csrf_token() }}';
                return $.ajax({
                    type: "POST",
                    url: "{{ secure_url('campaign/' . $campaign->campaign_id . '/add-recipient') }}",
                    data: item
                });
            },

            updateItem: function(item) {
                item._token = '{{ csrf_token() }}';
                return $.ajax({
                    type: "PUT",
                    url: "{{ secure_url('campaign/' . $campaign->campaign_id . '/update-recipient') }}",
                    data: item
                });
            },

            deleteItem: function(item) {
                item._token = '{{ csrf_token() }}';
                return $.ajax({
                    type: "DELETE",
                    url: "{{ secure_url('campaign/' . $campaign->campaign_id . '/remove-recipient') }}",
                    data: item
                });
            }
        },
        onError: function(args) {
            var errors = "";
            $.each(args.args[0].responseJSON, function (index, responseItem) {
                errors += responseItem[0] + "\n";
            });
            sweetAlert("Oops...", errors, "error");
        },
        onItemInvalid: function(args) {
            var error_list = "";
            $.each(args.errors, function(index, error) {
                error_list += error.field.name + ": " + error.message + "\n";
            });

            //console.log(error_list);

            sweetAlert("Oops...", error_list, "error");
        },
        fields: [
            {
                name: "first_name",
                type: "text",
                validate: "required"
            },
            {
                name: "last_name",
                type: "text",
                validate: "required"
            },
            { name: "email", type: "text", width: 200 },
            { name: "phone", type: "number" },
            { name: "address1", type: "text" },
            { name: "city", type: "text" },
            { name: "state", type: "text", width: 50 },
            { name: "zip", type: "text", width: 60 },
            { name: "year", type: "number", width: 70 },
            { name: "make", type: "text", width: 80 },
            { name: "model", type: "text" },
            { name: "vin", type: "text", width: 150 },
            { type: "control" }],
        data: recipient_data,
    });

    $("#pager").on("change", function() {
        var page = parseInt($(this).val(), 10);
        $("#jsGrid").jsGrid("openPage", page);
    });

    $("#upload-button-grp").click(function () {
        $("input[name=recipient_csv]").click();
    });

    var targetTemplate = $('#target-template').html();
    var groups = [];
    var groupSize = 100; // rows at a time
    var targets = [];
    var totalTargets = 0;
    var csvError = false;

    $('input[name=recipient_csv]').change(function(){
        var file = $(this)[0].files[0];
        console.log(file);

        if(file.type != 'text/csv')
        {
            sweetAlert('Uh oh', 'The file type you selected is invalid. Please choose a CSV file.', 'error');
            return;
        }

        console.log('made it past extension validation');

        $(this).parse({
            config: {
                header: true,
                complete: function(results, file) {
                    //remove lines that lack any details
                    for (var key in results.data) {
                        if(results.data[key]['last_name'] != undefined)
                        {
                            targets.push(results.data[key]);
                            totalTargets++;
                        }
                    }

                    //validate the CSV headers
                    var keys = Object.keys(targets[0]);
                    var fields = ['first_name','last_name','address1','city','state','zip','email','phone','year','make','model','vin'];

                    for (i = 0; i < keys.length; i++) {
                        if(fields.indexOf(keys[i]) == -1) {
                            sweetAlert('Uh oh', 'Please check your CSV file for unsupported column names.', 'error');
                            location.reload();

                            document.getElementById('csv-import-form').reset();
                            csvError = true;
                            return;
                        }
                    }
                    $('#filename').html(file.name+' <small>[ '+Math.round(parseFloat(file.size/1024),2)+'kb ]</small>')
                }
            },
            complete: function() {
                if(csvError) // end on error
                {
                    console.log(csvError);
                    csvError = false;
                    return;
                }
                var progressBar = $('#targetProgressBar');
                progressBar.closest('tr').show();
                progressBar.show();
                $('#targetProgressBar > .status').html('Processing 0 of '+totalTargets+' records');
                processTargets(targets);
                $("input[name=recipient_csv]").val("");
                sweetAlert('Done', 'Upload Complete!', 'success');
                location.reload();
            }

        });
    });

    function processTargets(targets)
    {
        if(targets.length > 0)
        {
            var batch = targets.splice(0, 100);
            console.log(batch);
            var diff = totalTargets - targets.length;
            $('#targetProgressBar > .status').html('Processing '+diff+' of '+totalTargets+' records');
            $('#targetProgressBar > .meter').css('width', Math.min(Math.floor((diff/totalTargets)*100), 100)+'%');

            $.ajax({
                url: "{{ secure_url('campaign/' . $campaign->campaign_id . '/recipients/upload') }}",
                type: "POST",
                data: {
                    targets:JSON.stringify(batch)
                },
                dataType: "json",
                success: function(data){
                    processTargets(targets);
                }
            });
        }
        else
        {
            $('#targetProgressBar > .status').html('Targets have been processed');
            $('#targetProgressBar > .meter').css('width', '100%');
            setTimeout(function(){
                honk.alert('Your targets have been imported.', function(){
                    location.reload();
                });
            }, 2000);
        }
    }
});
