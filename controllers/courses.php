<?php

require_once 'moocip_controller.php';

class CoursesController extends MoocipController {

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $this->cid = $this->plugin->getContext();
    }

    public function index_action()
    {
        $sem_class = \Mooc\SemClass::getMoocSemClass();
        $this->courses = $sem_class->getCourses();
    }

    public function show_action($cid)
    {
        if (strlen($cid) !== 32) {
            throw new Trails_Exception(400);
        }

        if (Navigation::hasItem("/course")) {
            Navigation::activateItem("/course/mooc_overview");
        } else {
            Navigation::activateItem("/mooc/overview");
        }

        $this->courseware = \Mooc\Courseware::findByCourse($cid);
    }
}
