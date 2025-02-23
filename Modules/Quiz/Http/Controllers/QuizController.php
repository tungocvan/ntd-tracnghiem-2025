<?php

namespace Modules\Quiz\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Quiz\Models\Question;
use Modules\Quiz\Models\QuestionSet;
use Modules\Category\Models\WpTerm;
use Modules\Category\Models\WpTermTaxonomy;
use Illuminate\Support\Str;
use App\Models\Option;
use Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionImport;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
         $this->middleware('permission:quiz-list|quiz-create|quiz-edit|quiz-delete', ['only' => ['index','show']]);
         $this->middleware('permission:quiz-create', ['only' => ['create','store']]);
         $this->middleware('permission:quiz-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:quiz-delete', ['only' => ['destroy']]);
    }

    public function quizList()
    {
        $questions = Question::all();

        $monhoc = Option::get_option('quiz_monhoc', []);
        $khoilop = Option::get_option('quiz_khoilop', []);
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        $dapan = Option::get_option('quiz_dapan', []);

        foreach ($questions as $question) {
            $question->parsed_details = parseQuestionDetails($question->question_details);
            $question->name_topic = $monhoc[$question->category_topic_id];
            $question->name_class = $khoilop[$question->category_class_id];
            $question->content = $question->parsed_details['content'];
            $btnEdit = "<button type='submit' class='btn btn-xs btn-default text-primary mx-1 shadow' name='edit' value='".$question->id."'>
                <i class='fa fa-lg fa-fw fa-pen'></i></button>";
            $btnDelete = "<button type='submit' class='btn btn-xs btn-default text-danger mx-1 shadow'  name='delete' value='".$question->id."'>
                <i class='fa fa-lg fa-fw fa-trash'></i></button>";
            $btnDetails = "<button type='submit' class='btn btn-xs btn-default text-teal mx-1 shadow'  name='detail' value='".$question->id."'>
                <i class='fa fa-lg fa-fw fa-eye'></i></button>";

            $question->action = $btnDetails.$btnEdit.$btnDelete  ;
        }

        $questions = $questions->select('id','content','name_topic','name_class','question_level','question_type','action');
        //dd($questions);
        return view('Quiz::quiz-list',compact('questions'));
    }
    public function submitList(Request $request)
    {
        $monhoc = Option::get_option('quiz_monhoc', []);
        $khoilop = Option::get_option('quiz_khoilop', []);
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        $dapan = Option::get_option('quiz_dapan', []);
        $user_id = $request->user()->id;
        if($request->delete){
            $questions = Question::find($request->delete);
            $questions->delete();
            return redirect()->route('quiz.quiz-list')
                        ->with('success','Question deleted successfully');
            //dd($questions->id);
        }
        if($request->edit){
            $questions = Question::find($request->edit);
            //dd($questions);
            $questions->parsed_details = parseQuestionDetails($questions->question_details);

            $user_id = $request->user()->id;


            $questions->question_level = array_search($questions->question_level, $capdo);
            $questions->question_type = array_search($questions->question_type, $loaicau);
            $questions->correct_answers = $questions->parsed_details['correct_answers'][0];

            $data = [
                'monhoc' => $monhoc,
                'khoilop' => $khoilop,
                'capdo' => $capdo ,
                'loaicau' => $loaicau,
                'dapan' => $dapan,
                'user_id' => $user_id,
                'id' => $questions->id
            ];
            return view('Quiz::quiz-edit',compact('questions','data'));
        }
        //dd($request->all());
        //echo "edit";
        if($request['loc-cau-hoi'] == "true"){
            $category_topic_id = $request['monhoc'];
            $category_class_id = $request['khoilop'];
            $question_level = $capdo[$request['capdo']];
            $question_type = $loaicau[$request['loaicau']];
            //dd($request->all());
            $questions = Question::where('category_topic_id',$category_topic_id)
            ->where('category_class_id',$category_class_id)
            ->where('question_level',$question_level)
            ->where('question_type',$question_type)
            ->get();

            if(count($questions)>0){
                foreach ($questions as $question) {
                    $question->parsed_details = parseQuestionDetails($question->question_details);
                    $question->name_topic = $monhoc[$question->category_topic_id];
                    $question->name_class = $khoilop[$question->category_class_id];
                    $question->content = $question->parsed_details['content'];
                    $question->action = "<input type='checkbox' class='select-row' name='chk-$question->id' value='$question->id' onclick='HanlderCheck(this,$question->id)' >";
                }
            }else{
                dd($questions);
            }


            $data = [
                'monhoc' => $monhoc,
                'khoilop' => $khoilop,
                'capdo' => $capdo ,
                'loaicau' => $loaicau,
                'dapan' => $dapan,
                'user_id' => $user_id,
                'questions' => $questions
            ];
         
            //dd($data);
            return view('Quiz::bode.topic-set-add',compact('data','category_topic_id','category_class_id','question_level','question_type'));
        }

        //return view('Quiz::quiz-list',compact('questions'));
    }

    public function topicSetList(Request $request)
    {

        $user_id = $request->user()->id;
        $monhoc = Option::get_option('quiz_monhoc', []);
        $khoilop = Option::get_option('quiz_khoilop', []);
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        $dapan = Option::get_option('quiz_dapan', []);



        $questions = Question::all();
        foreach ($questions as $question) {
            $question->parsed_details = parseQuestionDetails($question->question_details);
        }

        $data = [
            'monhoc' => $monhoc,
            'khoilop' => $khoilop,
            'capdo' => $capdo ,
            'loaicau' => $loaicau,
            'dapan' => $dapan,
            'user_id' => $user_id,
            'questions' => $questions
        ];

        return view('Quiz::bode.topic-set-list',compact('data'));
    }
    public function topicSetAdd(Request $request)
    {

        $method = $request->method();

        $user_id = $request->user()->id;
        $monhoc = Option::get_option('quiz_monhoc', []);
        $khoilop = Option::get_option('quiz_khoilop', []);
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        $dapan = Option::get_option('quiz_dapan', []);


        $questions = Question::all();
        foreach ($questions as $question) {
            $question->parsed_details = parseQuestionDetails($question->question_details);

        }

        $data = [
            'monhoc' => $monhoc,
            'khoilop' => $khoilop,
            'capdo' => $capdo ,
            'loaicau' => $loaicau,
            'dapan' => $dapan,
            'user_id' => $user_id,

        ];

        if($method == 'POST'){
            dd($questions->where('category_topic_id',$request->monhoc));
        }

        return view('Quiz::bode.topic-set-add',compact('data'));
    }
    public function questionSet($id)
    {

        // Lấy bộ đề theo ID
        $questionSet = QuestionSet::find($id);
        $timeRemaining = $questionSet->timeRemaining;    
        $questions = parseQuestions($questionSet->questions);
        //$questions = unserialize($questionSet->questions);
        //  dd($questions);
        // $questions = Question::all();
        // foreach ($questions as $question) {
        //     $question->parsed_details = parseQuestionDetails($question->question_details);
        // }
        return view('Quiz::quiz-set',compact('questions','id','timeRemaining'));
    }
    public function createSetquiz(Request $request)
    {

        //dd($request->all());
        // $id_bode = json_decode($request['bo_de'],true);
        // foreach($id_bode as $key => $value){

        // }
        //dd($bode);
        $user_id = $request->user()->id;
        $category_topic_id = $request['bd_monhoc'];
        $category_class_id = $request['bd_khoilop'];
        $question_level = $request['bd_capdo'];
        $question_type = $request['bd_loaicau'];
        $total_questions = $request['bd_socau'];
        $thoi_gian = $request['tg_bode'];
        $ten_bode = $request['ten_bode'];
        $id_bode = json_decode($request['bo_de'],true);

        $questions = Question::where('category_topic_id',$category_topic_id)
            ->where('category_class_id',$category_class_id)
            ->where('question_level',$question_level)
            ->where('question_type',$question_type)
            ->where('question_type',$question_type)
            ->whereIn('id',$id_bode)
            ->get();

        //dd($questions);
        $questionArray = [];
        foreach($questions as $question){
            array_push($questionArray,[$question['question_details']]);
        }
        $result = array_map(function($item) {
            return $item[0];
        }, $questionArray);
    
        $questionStr = implode(',',$result);
       // $questionStr = json_encode($questionArray,true);
        //$questionStr = serialize($questionArray);

        // $result = json_decode($questionStr);
        // dd($result);
        $data = [
            'category_topic_id' => $category_topic_id,
            'category_class_id' => $category_class_id,
            'question_type' => $question_type,
            'user_id' => $user_id,
            'timeRemaining' => $thoi_gian,
            'name' => $ten_bode,
            'total_questions' => count($result),
            'questions' => $questionStr
        ];

       
        $question = questionSet::create($data);
        return redirect()->route('quiz.topic-set-list')->with('success', 'Đã thêm bộ đề thành công.');

        dd($data);

        return view('Quiz::quiz-set',compact('questions','id','timeRemaining'));
    }


    public function submit(Request $request)
    {
        // Lấy tất cả câu hỏi
        $questions = Question::all();
        $results = [];

        foreach ($questions as $question) {
            $question->parsed_details = parseQuestionDetails($question->question_details);

            // Lấy đáp án người dùng đã chọn từ request
            $userAnswer = $request->input('question_' . $question->id);

            // Kiểm tra kết quả
            $correctAnswers = $question->parsed_details['correct_answers'];
            $isCorrect = in_array($userAnswer, $correctAnswers);

            // Lưu kết quả vào mảng results
            $results[] = [
                'question' => $question->parsed_details['content'],
                'correct' => $isCorrect,
                'user_answer' => $userAnswer,
                'correct_answers' => $correctAnswers,
                'parsed_details' => $question->parsed_details
            ];
        }

        // Trả về view kết quả
        return view('Quiz::result', ['results' => $results]);
    }
    public function submitSet(Request $request,$id)
    {
        // Lấy danh sách câu hỏi để kiểm tra đáp án
        $questionSet = QuestionSet::find($id);
        $questions = parseQuestions($questionSet->questions);
       // $questions = json_decode($questionSet->questions,true);
        // Đáp án đã chọn của người dùng
        $userAnswers = $request->input('answers');

        // Lưu kết quả cho mỗi câu hỏi
        $results = [];
        //dd($questions);
        foreach ($questions as $index => $question) {
            $correctAnswer = $question['correct_answer'];
            $userAnswer = isset($userAnswers[$index]) ? $userAnswers[$index] : null;

            // Kiểm tra đáp án đúng/sai
            $isCorrect = ($userAnswer == $correctAnswer);

            // Lưu kết quả
            $results[] = [
                'question' => $question['content'],
                'answers' => $question['answers'],
                'correct_answer' => $correctAnswer,
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
            ];
        }
        // Trả về view kết quả
        return view('Quiz::result-set', ['results' => $results]);
    }

    public function settings(Request $request)
    {
        // $monhoc = WpTermTaxonomy::where('taxonomy', 'topic_cat')
        // ->join('wp_terms', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
        // ->select('wp_terms.name','wp_terms.slug', 'wp_terms.term_id', 'wp_term_taxonomy.parent', 'wp_term_taxonomy.description')
        // ->get();
        $topic = Option::get_option('quiz_monhoc', []);
        $class = Option::get_option('quiz_khoilop', []);
        //dd($topic);
        return view('Quiz::settings',compact('topic','class'));
    }

    public function submitTopic(Request $request){

        //dd($request->all());

        $topic = Option::get_option('quiz_monhoc', []);
        $class = Option::get_option('quiz_khoilop', []);

        if($request['edit']){
          $id =$request['edit'];
          if($request['input-edit-topic']){
            $topic[$id] = $request['input-edit-topic'];
            Option::set_option('quiz_monhoc',$topic);
          }
          if($request['input-edit-class']){
            $class[$id] = $request['input-edit-class'];
            Option::set_option('quiz_khoilop',$class);
          }


          return redirect()->route('quiz.settings')->with('success', 'Cập nhật thành công.');
        }
        if($request['delete']){
            $id =$request['delete'];
            //dd($request->all());
            if($request['input-edit-topic']){
                unset($topic[$id]);
                //dd($topic);
                Option::set_option('quiz_monhoc',$topic);
            }
            if($request['input-edit-class']){
                unset($class[$id]);
                Option::set_option('quiz_khoilop',$class);
            }
            return redirect()->route('quiz.settings')->with('success', 'Xóa thành công.');
        }


    }
    public function submitClass(Request $request){

        if($request->edit){
            $result = json_decode($request->edit, true);
            echo "edit";
            dd($result);
        }
        if($request->delete){
            $result = json_decode($request->delete, true);
            echo "delete";
            dd($result);
        }
    }
    public function submitAdd(Request $request){

        //dd($request->all());
        //$parent = WpTerm::where('slug','')->first();
        $name = '';
        if($request['input-add-topic']){
            $name = $request['input-add-topic'];
            $topic = Option::get_option('quiz_monhoc', []);
            if (in_array($name, $topic)) {
                return redirect()->route('quiz.settings')->with('success', "$name  đã tồn tại");
            }

            array_push($topic, $name);
            Option::set_option('quiz_monhoc',$topic);
            return redirect()->route('quiz.settings')->with('success', "Thêm mới $name thành công.");

        }
        if($request['input-add-class']){
            $name = $request['input-add-class'];
            $class = Option::get_option('quiz_khoilop', []);

            if (in_array($name, $class)) {
                return redirect()->route('quiz.settings')->with('success', "$name  đã tồn tại");
            }

            array_push($class, $name);
            Option::set_option('quiz_khoilop',$class);
            return redirect()->route('quiz.settings')->with('success', "Thêm mới $name thành công.");
        }


        return redirect()->route('quiz.settings')->with('success', 'Vui lòng nhập dữ liệu.');
    }



    /**
     * Show the form for creating a new resource.
     */
    public function quizAdd(Request $request)
    {

        $user_id = $request->user()->id;
        $monhoc = Option::get_option('quiz_monhoc', []);
        $khoilop = Option::get_option('quiz_khoilop', []);
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        $dapan = Option::get_option('quiz_dapan', []);

        $data = [
            'monhoc' => $monhoc,
            'khoilop' => $khoilop,
            'capdo' => $capdo ,
            'loaicau' => $loaicau,
            'dapan' => $dapan,
            'user_id' => $user_id
        ];
        return view('Quiz::quiz-add', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function quizStore(Request $request)  {


        $question = [
            'user_id' => $request->user_id,
            'category_class_id' => $request->khoilop,
            'category_topic_id' => $request->monhoc,
            'question_details' => "[$request->content][$request->dapan1]|$request->dapan2|$request->dapan3|$request->dapan4][$request->dapan]",
            'question_type' => $request->capdo,
            'question_level' => $request->loaicau
        ];



        $validatorQuestion = Validator::make($request->all(), [
            'content' => ['required', 'string','min:1'],
            'dapan1' => ['required', 'string','min:1'],
            'dapan2' => ['required', 'string','min:1'],
            'dapan3' => ['required', 'string','min:1'],
            'dapan4' => ['required', 'string','min:1'],
            'dapan' => ['required'],

        ]);


        $validator = Validator::make($question, [
            'user_id' => ['required', 'numeric'],
            'category_class_id' => ['required', 'numeric'],
            'category_topic_id' => ['required', 'numeric'],
            'question_type' => ['required', 'string'],
            'question_level' => ['required', 'string'],
            'question_details' => ['required', 'string']
        ]);



        if($validator->fails() || $validatorQuestion->fails()){
            return redirect()->route('quiz.quiz-add')->with('question', 'Vui lòng cập nhật đầy đủ thông tin nội dung câu hỏi.')->withInput();  // Trả lại dữ liệu cũ đã submit;
        }


        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        $question['question_level'] = $capdo[$question['question_level']];
        $question['question_type'] = $loaicau[$question['question_type']];
        // dd($question);
        $question = Question::create($question);
        return redirect()->route('quiz.quiz-list')->with('success', 'Đã thêm câu hỏi thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function quizEdit(Request $request)
    {
        //dd($request->all());
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);

        $question = [
            'user_id' => $request->user_id,
            'category_class_id' => $request->khoilop,
            'category_topic_id' => $request->monhoc,
            'question_details' => "[$request->content][$request->dapan1]|$request->dapan2|$request->dapan3|$request->dapan4][$request->dapan]",
            'question_type' => $request->capdo,
            'question_level' => $request->loaicau
        ];

        $question['question_level'] = $capdo[$question['question_level']];
        $question['question_type'] = $loaicau[$question['question_type']];
        //dd($question);

        $validatorQuestion = Validator::make($request->all(), [
            'content' => ['required'],
            'dapan1' => ['required'],
            'dapan2' => ['required'],
            'dapan3' => ['required'],
            'dapan4' => ['required'],
            'dapan' => ['required'],
        ]);


        $validator = Validator::make($question, [
            'user_id' => ['required', 'numeric'],
            'category_class_id' => ['required', 'numeric'],
            'category_topic_id' => ['required', 'numeric'],
            'question_type' => ['required'],
            'question_level' => ['required'],
            'question_details' => ['required']
        ]);

        // dd($question);

        if($validator->fails()){
            return redirect()->route('quiz.submit-list')->with('question', 'Vui lòng cập nhật đầy đủ thông tin nội dung câu hỏi.')->withInput();  // Trả lại dữ liệu cũ đã submit;
        }

        $id = $request->id;

        $questionUpdate = Question::find($id);


        $questionUpdate->update($question);

        return redirect()->route('quiz.quiz-list')->with('success', 'Đã sửa câu hỏi thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function quizDelete(string $id)
    {
        dd($id);
    }
    public function quizImport(Request $request)
    {
        $user_id = $request->user()->id;
        $monhoc = Option::get_option('quiz_monhoc', []);
        $khoilop = Option::get_option('quiz_khoilop', []);
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        $dapan = Option::get_option('quiz_dapan', []);
        $data = [];


        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $import = new QuestionImport();
        $dataQuestion = Excel::toArray($import, $request->file('file'));
        $question_details = [];
        if(count($dataQuestion) > 0 ){
            foreach ($dataQuestion[0]  as $key => $value) {
                if($key != 0){
                    $content = $value[1];
                    $dapan1 = $value[2];
                    $dapan2 = $value[3];
                    $dapan3 = $value[4];
                    $dapan4 = $value[5];
                    $anwser = $value[6];
                    $question_details[$key] = "[$content][$dapan1|$dapan2|$dapan3|$dapan4][$anwser]";
                }
            }

            $data = [
                'monhoc' => $monhoc,
                'khoilop' => $khoilop,
                'capdo' => $capdo ,
                'loaicau' => $loaicau,
                'dapan' => $dapan,
                'user_id' => $user_id,
                'question_details' => serialize($question_details),
                'dataQuestion' => $dataQuestion[0]
            ];
        }

        return view('Quiz::quiz-import',compact('data'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function quizImportSet(Request $request)
    {

        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'khoilop' => ['required'],
            'monhoc' => ['required'],
            'loaicau' => ['required'],
            'capdo' => ['required'],
        ]);



        if($validator->fails()){
           return redirect()->route('quiz.quiz-list')->with('question', 'Vui lòng cập nhật đầy đủ thông tin nội dung câu hỏi.')->withInput();  // Trả lại dữ liệu cũ đã submit;

        }


        $Question = unserialize($request->question_details);
        $capdo = Option::get_option('quiz_capdo', []);
        $loaicau = Option::get_option('quiz_loaicau', []);
        foreach ($Question as $key => $value) {
            $question = [
                'user_id' => $request->user_id,
                'category_class_id' => $request->khoilop,
                'category_topic_id' => $request->monhoc,
                'question_details' => $value,
                'question_type' =>  $loaicau[$request->loaicau],
                'question_level' =>$capdo[$request->capdo]
            ];
            //dd($question);
            Question::create($question);
        }

        return redirect()->route('quiz.quiz-list')->with('success', 'Đã thêm câu hỏi thành công.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
