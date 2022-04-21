<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**This function create task of user authenticate
     * @param Illuminate\Http\Request
     * @return Illuminate\Http\Response;
     */
    public function createTask(Request $request)
    {
        $request->validate([
            'title' => 'required|max:25|string',
            'description' => "required|string",
            'date_of_end' => "required|date"
        ]);
        $task = Task::make([
            "title" => $request->title,
            "description" => $request->description,
            "date_of_end" => $request->date_of_end,
        ]);
        $task->user()->associate(Auth::user());
        $task->save();
        return response([
            'message' => 'Task create succefuly',
            'task' => $task
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
            'task' => $task
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
        $task_delete =Task::destroy($id);
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
            'tasks create' =>$tasks
        ]);
    }

    /**This function return all tasks create
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/

    public function index()
    {
        return Task::all();
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
            'task_update' => $task
        ],200);
     }

     /**This function return all tasks to do(status==0) by user
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/
     public function getAllTasksToDoByUser(Request $request)
     {
            $tasks = Task::where('user_id',Auth::user()->id)->where('status',0)->get();
            if(empty($tasks))
            {
                return response([
                    'message' => 'Empty, user not get task to do',
                ],200);
            }
            return response([
                'tasks to do' => $tasks
            ],200);
     }

     /**This function return all tasks doing(status==1) by user
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/
    public function getAllTasksDoingByUser(Request $request)
    {
           $tasks = Task::where('user_id',Auth::user()->id)->where('status',1)->get();
           if(empty($tasks))
           {
               return response([
                   'message' => 'Empty, user not get task doing',
               ],200);
           }
           return response([
               'tasks doing' => $tasks
           ],200);
    }


     /**This function return all tasks end(status==2) by user
     @param Illuminate\Http\Request
     * @return Illuminate\Http\Response*/
    public function getAllTasksEndByUser(Request $request)
    {
           $tasks = Task::where('user_id',Auth::user()->id)->where('status',2)->get();
           if(empty($tasks))
           {
               return response([
                   'message' => 'Empty, user not get task end',
               ],200);
           }
           return response([
               'tasks end' => $tasks
           ],200);
    }
}

