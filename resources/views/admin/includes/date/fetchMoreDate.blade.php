<script>
    function addMore(){
        let select = document.getElementById("lastDate");
        let date = select.getAttribute('data-lastdate');
        let urlLink = '{!! $url !!}';
        let model = '{!! $model !!}';
        if(date){
            $.ajax({
                url: "{{ route('general.more.date') }}?date=" + date + "&url=" + urlLink + "&model=" + model,
                method: 'GET',
                success: function(data) {
                    $("#addmore").append(data.data);
                    document.getElementById("lastDate").setAttribute('data-lastdate', data.end_date);
                }
            });
        }
    }
</script>
