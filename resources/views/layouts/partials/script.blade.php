<!-- SCRIPTS -->

<!-- Bootstrap tooltips -->
<script type="text/javascript" src="{{ URL::asset('js/tether.min.js') }}"></script>

<!-- Bootstrap core JavaScript -->
<script type="text/javascript" src="{{ URL::asset('js/bootstrap.min.js') }}"></script>

<!-- MDB core JavaScript -->
<script type="text/javascript" src="{{ URL::asset('js/mdb.min.js') }}"></script>

<script type="text/javascript" src="{{ URL::asset('js/script.js') }}"></script>

@if (!Auth::guest())
<script>
    $(".button-collapse").sideNav();

    var el = document.querySelector('.custom-scrollbar');

    Ps.initialize(el);
</script>
@endif 
