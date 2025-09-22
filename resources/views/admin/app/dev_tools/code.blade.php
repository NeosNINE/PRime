@extends('admin.app.dev_tools.layout')

@section('dev_tools_content')

    <div class="box-body">

        <h6 class="mt-10">Helpers</h6>
        <div class="btn-text-blocks">
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.refresh_helpers') }}" class="btn btn-primary btn-action" data-show-response-toastr data-show-error-as-offcanvas data-size="large"><i class="fa fa-refresh"></i> Refresh Helpers.php</a>
                <p>Обновляет файл /app/Extra/Helpers/Helpers.php на основе HelperClass.</p>
            </div>
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.ide_helpers') }}" class="btn btn-warning btn-action" data-show-response-toastr data-show-error-as-offcanvas data-size="large"><i class="fa fa-refresh"></i> Refresh IDE Helpers</a>
                <p>Обновляет Helpers для IDE.</p>
            </div>
        </div>

        <h6>Database / Models</h6>
        <div class="btn-text-blocks">
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.models_schema_generate') }}" class="btn btn-primary btn-action" data-show-response-toastr data-show-error-as-offcanvas data-size="large"><i class="fa fa-table"></i> Schema Generate</a>
                <p>Обновляет файлы Schema (.json файлы) всех моделей.</p>
            </div>
        </div>

        <h6>Localization</h6>
        <div class="btn-text-blocks">
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.localization_refresh') }}" class="btn btn-primary btn-action" data-show-response-toastr data-show-error-as-offcanvas data-size="large"><i class="fa fa-refresh"></i> Refresh</a>
                <p>Обновить все файлы локалей в соответствии с текущими данными в базе данных.</p>
            </div>
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.localization_load_to_db') }}" class="btn btn-warning btn-action" data-show-response-toastr data-show-error-as-offcanvas data-size="large"><i class="fa fa-download"></i> Load To Database</a>
                <p>Выгрузить локали из файлов в базу данных.</p>
            </div>
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.localization_check') }}" class="btn btn-success btn-action" data-show-response-toastr data-show-error-as-offcanvas data-size="large"><i class="fa fa-check-double"></i> Check</a>
                <p>Проверить локализацию, найти потенциальные ошибки.</p>
            </div>
        </div>

        <h6>Npm</h6>
        <div class="btn-text-blocks">
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.npm_run_dev') }}" class="btn btn-success btn-action" data-show-response-offcanvas data-show-error-as-offcanvas data-size="large"><i class="fa-solid fa-file-code mr-5"></i> npm run dev</a>
                <p>Запустить сборщик npm development.</p>
            </div>
        </div>

        <h6>Queue</h6>
        <div class="btn-text-blocks">
            <div class="btn-text-block">
                <a href="{{ route('admin.dev_tools.queue_check') }}" class="btn btn-success btn-action" data-show-response-toastr data-show-error-as-offcanvas data-size="large"><i class="fa fa-check-double"></i> Check</a>
                <p>Проверить запушен ли демон, который обрабатывает очереди.</p>
            </div>
            <div class="btn-text-block">
                <a href="#" class="btn btn-warning" data-queue-start><i class="fa fa-play"></i> Start</a>
                <p>Запустить Queue worker.</p>
            </div>
        </div>


    </div>

@endsection
