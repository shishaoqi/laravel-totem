@extends('totem::layout')
@section('page-title')
    @parent
    - Task
@stop
@section('title')
    <div class="uk-flex uk-flex-between uk-flex-middle">
        <h5 class="uk-card-title uk-margin-remove">{{ trans('totem::tasks.task_details') }}</h5>
        <status-button :data-task="{{ $task }}" :data-exists="{{ $task->exists ? 'true' : 'false' }}" activate-url="{{route('totem.task.activate')}}" deactivate-url="{{route('totem.task.deactivate', $task)}}"></status-button>
    </div>
@stop
@section('main-panel-content')
    <ul class="uk-list uk-list-striped">
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.description') }}</span>
            <span class="uk-float-left">{{str_limit($task->description, 80)}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.command') }}</span>
            <span class="uk-float-left">{{$task->command}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.parameters') }}</span>
            <span class="uk-float-left">{{$task->parameters or 'N/A'}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.cron_expression') }}</span>
            <span class="uk-float-left">
                <span>{{$task->getCronExpression()}}</span>
            </span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.timezone') }}</span>
            <span class="uk-float-left">{{$task->timezone}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.created_at') }}</span>
            <span class="uk-float-left">{{$task->created_at->toDateTimeString()}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.updated_at') }}</span>
            <span class="uk-float-left">{{$task->updated_at->toDateTimeString()}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.email_notification') }}</span>
            <span class="uk-float-left">{{$task->notification_email_address or 'N/A'}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.SMS_notification') }}</span>
            <span class="uk-float-left">{{$task->notification_phone_number or 'N/A'}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.slack_notification') }}</span>
            <span class="uk-float-left">{{$task->notification_slack_webhook or 'N/A'}}</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.average_runtime') }}</span>
            <span class="uk-float-left">{{$task->results->count() > 0 ? number_format(  $task->results->sum('duration') / (1000 * $task->results->count()) , 2) : '0'}} seconds</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">{{ trans('totem::tasks.next_run_schedule') }}</span>
            <span class="uk-float-left">{{$task->upcoming }}</span>
        </li>
        @if($task->dont_overlap)
            <li>
                <span class="uk-float-left">Doesn't Overlap with another instance of this task</span>
            </li>
        @endif
        @if($task->run_in_maintenance)
            <li>
                <span class="uk-float-left">{{ trans('totem::tasks.run_in_maintenance_mode') }}</span>
            </li>
        @endif
    </ul>
@stop
@section('main-panel-footer')
    <div class="uk-flex uk-flex-between uk-flex-middle">
        <span>
            <a href="{{ route('totem.task.edit', $task) }}" class="uk-button uk-button-primary uk-button-small">{{ trans('totem::tasks.edit') }}</a>
            <form class="uk-display-inline" action="{{route('totem.task.delete', $task)}}" method="post">
                {{ csrf_field() }}
                {{ method_field('delete') }}
                <button type="submit" class="uk-button uk-button-danger uk-button-small">{{ trans('totem::tasks.delete') }}</button>
            </form>
        </span>
        <execute-button :data-task="{{ $task }}" url="{{route('totem.task.execute', $task)}}" button-class="uk-button-small uk-button-primary"></execute-button>
    </div>
@stop
@section('additional-panels')
    <div class="uk-card uk-card-default uk-margin-top">
        <div class="uk-card-header">
            <h5 class="uk-card-title uk-margin-remove">{{ trans('totem::tasks.execution_results') }}</h5>
        </div>
        <div class="uk-card-body uk-padding-remove-top">
            <table class="uk-table uk-table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('totem::tasks.executed_at') }}</th>
                        <th>{{ trans('totem::tasks.duration') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($results = $task->results()->orderByDesc('created_at')->paginate(10) as $result)
                    <tr>
                        <td>{{$result->ran_at->toDateTimeString()}}</td>
                        <td>{{ number_format($result->duration / 1000 , 2)}} seconds</td>
                        <td>
                            <task-output output="{{nl2br($result->result)}}"></task-output>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="uk-text-center" colspan="5">
                            <p>Not executed yet.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="uk-card-footer">
            {{$results->links('totem::partials.pagination')}}
        </div>
    </div>
@stop