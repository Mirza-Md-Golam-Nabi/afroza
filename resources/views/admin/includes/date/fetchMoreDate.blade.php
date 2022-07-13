<script>
    function addMore(){
        let select = document.getElementById("lastDate");
        let date = select.getAttribute('data-lastdate');
        let urlLink = '{!! $url !!}';
        if(date){
            $.ajax({
                url: "{{ route('general.more.date') }}?date=" + date + "&url=" + urlLink,
                method: 'GET',
                success: function(data) {
                    $("#addmore").append(data.data);
                    document.getElementById("lastDate").setAttribute('data-lastdate', data.end_date);
                }
            });
        }
    }
</script>
