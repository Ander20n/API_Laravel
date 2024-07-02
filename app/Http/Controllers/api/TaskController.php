<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{

  public function index()
  {
    return Task::all();
  }

  public function store(Request $request)
  {
    try {
      $validatedData = $request->validate([
        'title' => 'required|string',
        'description' => 'required|nullable|string',
        'dueData' => 'required|nullable|date',
        'status' => 'required|nullable|string',
        'project_id' => 'required|integer|exists:projects,id',
      ]);

      $task = Task::create($validatedData);
      return response()->json([
        'message' => 'Tarefa criada com sucesso!',
        'task' => $task,
      ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'message' => 'Erro de validação.',
        'errors' => $e->validator->errors(),
      ], 422);

    } catch (Exception $e) {
      return response()->json([
        'message' => 'Falha ao criar a tarefa.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function show($id)
  {
    try {
        return Task::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Tarefa não encontrada!',
            'error' => $e->getMessage(),
        ], 404);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $task = Task::findOrFail($id);
      $validatedData = $request->validate([
        'title' => 'required|string',
        'description' => 'required|nullable|string',
        'dueData' => 'required|nullable|date',
        'status' => 'required|nullable|string',
        'project_id' => 'required|integer|exists:projects,id',
      ]);

      $task->update($validatedData);
      return response()->json([
        'message' => 'Tarefa atualizada com sucesso!',
        'task' => $task,
      ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'message' => 'Erro de validação.',
        'errors' => $e->validator->errors(),
      ], 422);

    } catch (ModelNotFoundException $e) {
      return response()->json([
        'message' => 'Tarefa não encontrada!',
        'error' => $e->getMessage(),
      ], 404);

    } catch (Exception $e) {
      return response()->json([
        'message' => 'Falha ao atualizar a tarefa.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  public function destroy($id)
  {
    try {
      $task = Task::findOrFail($id);
      $task->delete();
      return response()->json([
        'message' => 'Tarefa deletada com sucesso!',
        'task' => $task,
      ], 200);

    } catch (ModelNotFoundException $e) {
      return response()->json([
        'message' => 'Tarefa não encontrada!',
        'error' => $e->getMessage(),
      ], 404);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'Falha ao deletar a tarefa.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

}