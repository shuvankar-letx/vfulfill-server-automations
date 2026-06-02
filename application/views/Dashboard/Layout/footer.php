<footer class=footer>
    <div class="d-sm-flex justify-content-center justify-content-sm-between"><span class="d-block d-sm-inline-block text-center text-muted text-sm-left">Copyright © 2025 <b><u>vInfra</u> Infrastructure Automation & Monitoring Platform for vFulfill</b></span></div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<?php if($this->session->flashdata('success')){ ?>

<script>

Swal.fire({

    icon: 'success',

    title: 'Success',

    text: '<?= $this->session->flashdata('success'); ?>'

});

</script>


<?php } 
if($this->session->flashdata('error')){ ?>

<script>

Swal.fire({

    icon: 'error',

    title: 'Error',

    text: '<?= $this->session->flashdata('error'); ?>'

});

</script>

<?php } ?>