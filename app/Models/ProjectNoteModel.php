<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectNoteModel extends Model
{
    protected $table = 'project_notes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['project_id', 'note', 'created_at'];

    protected $validationRules = [
        'project_id' => 'required|integer',
        'note' => 'required|min_length[1]'
    ];

    protected $validationMessages = [
        'project_id' => [
            'required' => 'Proje ID gereklidir.',
            'integer' => 'Geçerli bir proje ID giriniz.'
        ],
        'note' => [
            'required' => 'Not içeriği gereklidir.',
            'min_length' => 'Not içeriği en az 1 karakter olmalıdır.'
        ]
    ];

    public function getProjectNotes($projectId)
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function addNote($projectId, $note)
    {
        return $this->insert([
            'project_id' => $projectId,
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function deleteNote($noteId)
    {
        return $this->delete($noteId);
    }
}



