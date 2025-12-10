@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h6>@lang('Config pusher credentials')</h6>
                </div>
                <form method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang("App ID")</label>
                                    <input type="text" class="form-control" value="{{ @gs('pusher_configuration')->app_id }}" name="app_id" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang("Key")</label>
                                    <input type="text" class="form-control" value="{{ @gs('pusher_configuration')->key }}" name="key" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang("Secret Key")</label>
                                    <input type="text" class="form-control" value="{{ @gs('pusher_configuration')->secret_key }}" name="secret_key" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang("Cluster")</label>
                                    <input type="text" class="form-control" name="cluster" value="{{ @gs('pusher_configuration')->cluster }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
