<?php

namespace projectFour\Http\Controllers;

use projectFour\Http\Controllers\Controller;
use projectFour\ListController;
use Illuminate\Http\Request;

class TaskController extends Controller {

    /**
    * Responds to requests to GET /tasks
    */
    public function getIndex() {

        $id = \Auth::user()->id;

        $allTasks = \DB::table('tasks')
            ->where('list_id', $id)
            ->where('complete', 0)
            ->get();


        $complete = \DB::table('tasks')
            ->where('list_id', $id)
            ->where('complete', 1)
            ->get();

        return view('tasks.tasks')
            ->with('tasks', $allTasks)
            ->with('complete', $complete);
    }

    /**
    * Responds to requests to GET /task/create
    */
    public function getCreate() {

        # Check to see if user has a list
        $list_status = \Auth::user()->list_status;

        # If user has list create task; if not create list
        if ($list_status == 1) {

            return view('tasks.create');

        } else {

            return redirect('/list/create');
        }
    }

    /**
    * Responds to requests to POST /task/create
    */
    public function postCreate(Request $request) {

        $this->validate($request,[
            'task' => 'required|max:100',
        ]);

        # Mass Assignment
        $data = $request->only('task');
        $data['list_id'] = \Auth::user()->id;

        #Add the data
        $task = new \projectFour\Task($data);
        $task->save();

        return redirect('/tasks');
    }

    /**
    * Responds to requests to GET /task/edit/{id?}
    */
    public function getEdit($id) {

        $task =  \projectFour\Task::findOrFail($id);

        return view('tasks.edit')->with('task', $task);

    }


    /**
    * Responds to requests to POST /task/edit/{id?}
    */
    public function postEdit(Request $request) {

        # Validate the input
        $this->validate($request,[
            'task' => 'required|max:100',
        ]);


        $id = $request->id;

        $oldTask = \projectFour\Task::find($id);

        $oldTask->task = $request->task;

        $oldTask->save();

        return redirect('/tasks');

    }

    /**
    * Responds to requests to GET /task/complete/{id?}
    */
    public function getComplete(Request $request, $id) {

        $id = $request->id;

        $oldTask = \projectFour\Task::find($id);

        $oldTask->complete = 1;

        $oldTask->save();

        return redirect('/tasks');

    }

    /**
	* Responds to requests to GET /task/delete/{id?}
	*/
    public function getDelete($id) {

        # Get the task to be deleted
        $task = \projectFour\Task::find($id);

        # Then delete the task
        $task->delete();

        return redirect('/tasks');

    }
}
