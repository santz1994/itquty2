@extends('layouts.app')

@section('main-content')
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">{{$pageTitle}}</h3>
        </div>
          <div class="box-body no-padding">
            <div class="mailbox-read-info">
              <h3>{{$ticket->subject}}</h3>
              <h5>{{$ticket->user->name}}
                <?php $createdDate = \Carbon\Carbon::parse($ticket->created_at); ?>
                <span class="mailbox-read-time pull-right">Ticket logged on {{$createdDate->format('l, j F Y, H:i')}}</span></h5>
            </div>
            <div class="mailbox-read-message">
              {!! nl2br(e($ticket->description)) !!}
            </div>
            <!-- /.mailbox-read-message -->
            <hr>
          <ul class="timeline">
            @foreach($ticketEntries as $ticketEntry)
							<?php $createdDate = \Carbon\Carbon::parse($ticketEntry->created_at); ?>
					    <!-- timeline item -->
					    <li>
				        <!-- timeline icon -->
				        <i class="fa fa-user bg-blue"></i>
				        <div class="timeline-item">
			            <span class="time">{{$createdDate->format('l, j F Y, H:i')}}</span>

			            <h3 class="timeline-header">{{$ticketEntry->user->name}}</h3>

			            <div class="timeline-body">
										<dl class="dl-horizontal">
				              <dt>Note:</dt><dd>{{$ticketEntry->note}}</dd>
										</dl>
			            </div>
			            <div class="timeline-footer">
			            </div>
				        </div>
				    	</li>
					    <!-- END timeline item -->
            @endforeach
					</ul>
          <div class="box-footer">
            @if((auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')) || $ticket->assigned_to == auth()->id())
              <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary">
                <i class="fa fa-edit"></i> Edit Ticket
              </a>
            @endif
            <button type="button" class="btn btn-default" id='note'><i class="fa fa-pencil"></i> Add Note</button>
            <div id='new-note' style='display: none'>
              <form method="POST" action="/tickets/{{$ticket->id}}">
                {{csrf_field()}}
                <div class="form-group">
                  <label for="note">New Note</label>
                  <textarea name="note" class="form-control" rows="5">{{old('note')}}</textarea>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary"><b>Add New Note</b></button>
                </div>
              </form>
            </div>
          </div>
          <div class="text-center"><a class="btn btn-primary" href="{{ URL::previous() }}">Back</a></div><br>
        </div>
      </div>
      
      <!-- File Attachments Section -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-paperclip"></i> Attachments</h3>
        </div>
        <div class="box-body">
          @include('partials.file-uploader', [
            'model_type' => 'ticket',
            'model_id' => $ticket->id,
            'collection' => 'attachments'
          ])
        </div>
      </div>
      
      @if(count($errors))
        <ul>
          @foreach($errors->all() as $error)
            <li>{{$error}}</li>
          @endforeach
        </ul>
      @endif
    </div>
  <script>
    $("#note").click(function() {
      $("#new-note").toggle('1500');
    });
  </script>
  @if(Session::has('status'))
    <script>
      $(document).ready(function() {
        Command: toastr["{{Session::get('status')}}"]("{{Session::get('message')}}", "{{Session::get('title')}}");
      });
    </script>
  @endif
@endsection


