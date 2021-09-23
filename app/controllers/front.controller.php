<?php

class frontController extends Controller
{
    private $coursesModel;
    public function __construct()
    {
        // $this->checkPermission();
        $this->coursesModel = new coursesModel();
        $this->_view();
    }

    private function _view()
    {
        $url = explode('/', trim($_SERVER['REQUEST_URI']));
        $view = end($url);
        if($view === ''){
            $view = 'index.php';
        }elseif(!empty($_GET)) {
            $view = explode('?', $view)[0];
        }
        $view = str_replace('.php', '', $view);
        $this->$view($view);
    }

    private function index($view)
    {
        $latestCourses = $this->coursesModel->getLatestCourses();
        $this->render($view, ['latest_courses' => $latestCourses]);
    }

    /* 
    =====================
    =       Courses     =
    =====================
    * Show all courses
    * can filter courses by categories
    */
    private function courses($view)
    {
        $categoriesModel = new coursesCategoriesModel();
        $data = array();
        $categories = $categoriesModel->getCategories();
        $courses = $this->coursesModel->getCourses();
        $data['courses'] = $courses;
        $data['categories'] = $categories;
        // $data['pageName'] = strtoupper($view);
        $this->render($view, $data);
    }



    private function about($view)
    {
        $this->render($view);
    }

    private function blog($view)
    {
        $this->render($view);
    }

    private function contact($view)
    {
        $this->render($view);
    }
}