@include('backend.post.catalogue.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('post.catalogue.destroy', $postCatalogue->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">{{ __('messages.generalTitle') }}</div>
                    <div class="panel-description">
                        <p>- {{ __('messages.deleteDescription') }} <span class="text-danger">{{ $postCatalogue->name }}</span></p>
                        <p>- {{ __('messages.deleteNote') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label">{{ __('messages.tableName') }} <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($postCatalogue->name) ?? '') }}" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-danger" type="submit" name="send" value="send">{{ __('messages.postCatalogue.delete.title') }}</button>
        </div>
    </div>
</form>