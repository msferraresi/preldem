<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use DB;

class NewsController extends Controller
{
    public function index(Request $request)
    {


        $team_selected = $request->team_id;
        $id_user = $request->id_user;

        $query = 'SELECT DISTINCT n.id, n.tittle, SUBSTRING(n.description ,1,50) description, n.user_id, group_concat(ca.name) name, DATE_FORMAT(n.created_at,\'%d-%m-%Y\') created_at
        FROM news n
        INNER JOIN news_teams nt ON n.id = nt.news_id
        INNER JOIN company_areas ca ON nt.company_area_id = ca.id
        INNER JOIN company_areas_users cau ON ca.id = cau.company_area_id
        INNER JOIN users u ON cau.user_id = u.id
        WHERE n.deleted_at IS NULL AND ca.team_id = ? AND u.id = ?
        GROUP BY n.id, n.tittle, SUBSTRING(n.description ,1,50), n.user_id, DATE_FORMAT(n.created_at,\'%d-%m-%Y\')
        ORDER BY DATE_FORMAT(n.created_at,\'%d-%m-%Y\') DESC;';


        $news = DB::select($query, [$team_selected, $id_user]);

        if (count(collect($news)) > 0) {
            return view('news.index', compact('news'));
        }else {
            return view('news.index', [
                'errorCode' => 400,
                'errorMessage' => 'No tiene equipo asociado.',
            ]);
        }

    }

    public function show(Request $request)
    {
        $new = News::where('id', $request->new_id)->get();
        //dd($new);
        return view('news.show', compact('new'));
    }
}
