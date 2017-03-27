@if($errors->hasBag('exception'))
    <?php $error = $errors->getBag('exception');?>
    <div class="alert alert-error alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <p>{!! $error->get('message')[0] !!}</p>
    </div>
@endif