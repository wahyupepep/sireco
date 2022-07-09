@extends('layouts.app')

@section('content')
    @include('layouts.overview',['text' => 'Detail Member '.$member->fullname.'', 'icon' => 'mdi mdi-view-agenda'])
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12 p-2">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                              <tr>
                                <td>
                                  Full Name
                                </td>
                                <td>:</td>
                                <td>{{$member->fullname}}</td>
                              </tr>
                              <tr>
                                <td>
                                  Name
                                </td>
                                <td>:</td>
                                <td>{{$member->name}}</td>
                              </tr>
                              <tr>
                                <td>
                                  Email
                                </td>
                                <td>:</td>
                                <td>{{$member->email}}</td>
                              </tr>
                              <tr>
                                <td>
                                  Birthdate
                                </td>
                                <td>:</td>
                                <td>{{is_null($member->birthdate) ? '-' : date('d M y', strtotime($member->birthdate))}}</td>
                              </tr>
                              <tr>
                                <td>
                                  Address
                                </td>
                                <td>:</td>
                                <td>{{is_null($member->address) ? '-' : $member->address }}</td>
                              </tr>
                              <tr>
                                <td>
                                  Work Type
                                </td>
                                <td>:</td>
                                <td>{{!is_null($member->work_type) ? \App\Models\User::WORK_TYPE[$member->work_type] : '-'}}</td>
                              </tr>
                              <tr>
                                <td>
                                    Company
                                </td>
                                <td>:</td>
                                <td>{{!is_null($member->industry_name) ? $member->industry_name : '-' }}</td>
                              </tr>
                              <tr>
                                <td>
                                    Hobby
                                </td>
                                <td>:</td>
                                <td>{{!is_null($member->hobby) ? \App\Models\User::HOBBY[$member->hobby] : '-' }}</td>
                              </tr>
                              <tr>
                                <td>
                                    Phone
                                </td>
                                <td>:</td>
                                <td>{{is_null($member->phone) ? '-' : $member->phone}}</td>
                              </tr>
                              <tr>
                                <td>
                                    Age
                                </td>
                                <td>:</td>
                                <td>{{!is_null($member->classification_age) ? \App\Models\User::AGE[$member->classification_age] . ' year' : '-' }}</td>
                              </tr>
                              <tr>
                                <td>
                                    Package
                                </td>
                                <td>:</td>
                                <td>{{is_null($member->package) ? '-' : $member->package->name}}</td>
                              </tr>
                              <tr>
                                <td>
                                    Date Valid Member
                                </td>
                                <td>:</td>
                                <td>{{!is_null($member->valid_date_member) ? date('d M y', strtotime($member->valid_date_member)) : '-'}}</td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <a href="{{route('member.index')}}" class="btn btn-success btn-back mt-3">Back</a>
               
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
      $(function () {
       
        
        
    });
</script>
@endsection