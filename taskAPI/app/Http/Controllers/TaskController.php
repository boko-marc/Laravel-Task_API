<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendEmailJob;

class TaskController extends Controller
{
    /**This function create task of user authenticate
     * @param Illuminate\Http\Request
     * @return Illuminate\Http\Response;
     */
    public function createTask(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|max:25|string',
        //     'description' => "required|string",
        //     'date_of_end' => "required|date_format:Y-m-d H:i:s|after_or_equal:start_date",
        //     'start_date' => "required|date_format:Y-m-d H:i:s|after_or_equal:today"
        // ]);
        $task = Task::make([
            "title" => $request->title,
            "description" => $request->description,
            "date_of_end" => $request->date_of_end,
            "start_date" => $request->start_date
        ]);
        $task->user()->associate(Auth::user());
        $task->save();

        $taskDateEnd = Carbon::createFromFormat('Y-m-d H:i:s', $task->date_of_end)->format('d m Y');
        $taskDateStart = Carbon::createFromFormat('Y-m-d H:i:s', $task->start_date)->format('d m Y');
        $timeToSendEmail = Carbon::createFromFormat('Y-m-d H:i:s', $task->date_of_end)->subhours(20);
        if($taskDateStart != $taskDateEnd)
        {     
            SendEmailJob::dispatch($task)->delayed($timeToSendEmail);
        }

        return response([
            'message' => 'Task create succefuly',
            'data' => $task
        ],201);
    }

    /**This function update task of user authenticate
     * @param Illuminate\Http\Request
     * @return Illuminate\Http\Response;
     */
    public function updateTask($id, Request $request)
    {
        $request->validate([
            'title' => 'required|max:25|string',
            'description' => "required|string",
            'date_of_end' => "required|date"
        ]);
        $task = Task::where('id',$id)->first();
        if(!$task || (Auth::user()->id != $task->user_id))
        {
            return response([
                'message' => 'Unautorized, this task not found or is not for you',
            ],400);
        }
        $task->update($request->all());
        return response([
            'message' => 'Task update succefuly',
            'data' => $task
        ],200);
    }

      /**This function delete a task of user authenticate
     * @param Illuminate\Http\Request
     * @return Illuminate\Http\Response;
     */
    public function deleteTask($id, Request $request)
    {
        $task = Task::where('id',$id)->first();
        if(!$task || (Auth::user()->id != $task->user_id))
        {
            return response([
                'message' => 'Unautorized, this task not found or is not for you',
            ],400);
        }
        $task->delete();
        return response([
            'message' => 'Task delete succefuly'
        ],200);
    }
     /**This function get all tasks create by user authenticate
     * @param Illuminate\Http\Request
     * @return Illuminate\Http\Response;
     */
    public function getAllTasksCreateByUser(Request $request)
    {
        $tasks = Task::where('user_id',Auth::user()->id)->get();
        return response([
            'data' =>$tasks
        ]);
    }

    /**This function return all tasks create
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/

    public function index()
    {
        return response([
        'result' =>Task::all(),
        'message' => 'All tasks'
        ]);

    }


    /**This function update task status
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/

     public function updateTaskStatus(Request $request,$id)
     {
         $request->validate([
            'status' => 'required|integer|min:0|max:2'
         ]);
         $task = Task::find($id);
         if(!$task || (Auth::user()->id != $task->user_id))
         {
             return response([
                 'message' => 'Unautorized, this task not found or is not for you',
             ],400);
         }
         $task->update($request->all());
         return response([
            'message' => 'Task status update succefuly',
            'result' => $task
        ],200);
     }

     /**This function return all tasks to do(status==0) by user
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/
     public function getAllTasksToDoByUser(Request $request)
     {
            $tasks = Task::where('user_id',Auth::user()->id)->where('status',0)->get();
            if(count($tasks) == 0 )
            {
                return response([
                    'message' => 'Empty, user not get task to do',
                ],200);
            }
            return response([
                'result' => $tasks
            ],200);
     }

     /**This function return all tasks doing(status==1) by user
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/
    public function getAllTasksDoingByUser(Request $request)
    {
           $tasks = Task::where('user_id',Auth::user()->id)->where('status',1)->get();
           if(count($tasks) == 0)
           {
               return response([
                   'message' => 'Empty, user not get task doing',
               ],200);
           }
           return response([
               'result' => $tasks
           ],200);
    }


     /**This function return all tasks end(status==2) by user
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/
    public function getAllTasksEndByUser(Request $request)
    {
           $tasks = Task::where('user_id',Auth::user()->id)->where('status',2)->get();
           if(count($tasks) == 0 )
           {
               return response([
                   'message' => 'Empty, user not get task end',
               ],200);
           }
           return response([
               'result' => $tasks
           ],200);
    }
}

