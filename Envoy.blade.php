@servers(['prod' => ['shipment@shipment-tracking.rubartagroup.co.id']])

@task('deploy', ['on' => 'prod'])
    cd apps/shipment-tracking
    git pull
    php artisan migrate --force
@endtask
