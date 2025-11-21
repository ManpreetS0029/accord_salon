@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Category</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array(   'route' => ['category.store' ])) }}
                            {{ csrf_field() }}
                                <div class="panel-body container-fluid">

                                    <div class="row row-lg">

                                        <div class="col-lg-6">

                                            <div class="form-group">
                                                <label>Name *</label>
                                                <input type="text" class=" form-control" name="name" id="name" value="">
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class=" form-control" name="description" id="description">{{ old('description') }}</textarea>
                                            </div>



                                        </div>
                                    </div>

                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <button type="reset" class="btn btn-primary">Reset</button>

                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>

                                </div></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
