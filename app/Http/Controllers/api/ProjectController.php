<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends Controller
{

    public function index()
    {
        return Project::all();
    }

    public function store(Request $request)
    {
        try {
            
            $validatedData = $request->validate([
                'name' => 'required|string',
                'description' => 'required|nullable|string',
                'startDate' => 'required|nullable|date',
                'endDate' => 'required|nullable|date|after:startDate',
            ]);

            $project = Project::create($validatedData);
            return response()->json([
                'message' => 'Projeto criado com sucesso!',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->validator->errors(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar o projeto.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            return Project::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Projeto não encontrado!',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $validatedData = $request->validate([
                'name' => 'required|string',
                'description' => 'required|nullable|string',
                'startDate' => 'required|nullable|date',
                'endDate' => 'required|nullable|date|after:startDate',
            ]);

            $project->update($validatedData);
            return response()->json([
                'message' => 'Projeto atualizado com sucesso!',
                'project' => $project,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->validator->errors(),
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Projeto não encontrado!',
                'error' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao atualizar o projeto.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->delete();
            return response()->json([
                'message' => 'Projeto deletado com sucesso!',
                'project' => $project,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Projeto não encontrado!',
                'error' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao deletar o projeto.',
                'error' => $e->getMessage(),
            ], 500);
        
        }
    }

    public function getTasks($id)
    {
        try {
            $project = Project::findOrFail($id);
            $tasks = $project->tasks;

            return response()->json([
                'message' => 'Tarefas do projeto recuperadas com sucesso!',
                'tasks' => $tasks,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Projeto não encontrado!',
                'error' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao recuperar as tarefas do projeto.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}