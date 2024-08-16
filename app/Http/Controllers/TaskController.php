<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
    use HttpResponses; //To use $this.error() method

    public function index()
    {
        return TaskResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );
    }

    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority
        ]);

        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        return $this->isNotAuthorizated($task) ? $this->isNotAuthorizated($task) : new TaskResource($task);

        /*Another possible way to do it
        //As parameter: $id
        $task = Task::where('id', $id)->get();
        return*/
    } 

    public function update(Request $request, Task $task)
    {
        if(Auth::user()->id !== $task->user_id){
            return $this->error('', 'You are not authorized to make this request', 403);
        }

        $task->update($request->all());
    
        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        return $this->isNotAuthorizated($task) ? $this->isNotAuthorizated($task) : $task->delete();
    }

    private function isNotAuthorizated($task)
    {
        if(Auth::user()->id !== $task->user_id){
            return $this->error('', 'You are not authorized to make this request', 403);
        }
    }
}
