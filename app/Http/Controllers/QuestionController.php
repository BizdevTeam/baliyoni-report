<?php

namespace App\Http\Controllers;


use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input tanggal mulai dan tanggal akhir dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        $questions = Question::query()
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan waktu pembuatan terbaru
            ->paginate(10)
            ->withQueryString();
    
        return view('ask.questions', compact('questions'));
    }

    public function store(Request $request)
{
    $request->validate([
        'question' => 'required|string|max:255',
        'asked_by' => 'required|string',
        'asked_to' => 'required|string',
    ]);

    // Adding the current date and time to the request data
    $data = $request->all();
    $data['created_at'] = now();  // Use Laravel's now() helper for the current date and time

    // Creating a new question with the additional created_at field
    Question::create($data);

    return redirect()->route('questions.index')->with('success', 'Data Berhasil Ditambahkan');
}

    public function storeAnswer(Request $request, $questionId)
    {
        $request->validate([
            'answer' => 'required|string',
        ]);

        Answer::create([
            'answer' => $request->answer,
            'question_id' => $questionId,
        ]);

        return redirect()->route('questions.index')->with('success', 'Data Berhasil Ditambahkan');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
        ]);

        $question = Question::findOrFail($id);
        $question->update([
            'question' => $request->question,
        ]);

        return redirect()->route('questions.index')->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return redirect()->route('questions.index')->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function updateAnswer(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string',
        ]);

        $answer = Answer::findOrFail($id);
        $answer->update([
            'answer' => $request->answer,
        ]);

        return redirect()->route('questions.index')->with('success', 'Jawaban berhasil diperbarui.');
    }

    public function destroyAnswer($id)
    {
        $answer = Answer::findOrFail($id);
        $answer->delete();

        return redirect()->route('questions.index')->with('success', 'Jawaban berhasil dihapus.');
    }
}
