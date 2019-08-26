<?php $__container->servers(['prod' => ['shipment@shipment-tracking.rubartagroup.co.id']]); ?>

<?php $__container->startTask('deploy', ['on' => 'prod'); ?>
    cd apps/shipment-tracking
    git pull
    php artisan migrate
<?php $__container->endTask(); ?>
