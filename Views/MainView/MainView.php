<?php
PathDriver::Using(array(PathDriver::VIEWS => array("View"),
	PathDriver::TABLES => array("TableController"),
	PathDriver::DRIVERS => array("BBDriver")));

class MainView extends View {
	public function __construct()
	{
		parent::__construct(static::class);
	}
	public function Main()
	{
		$Story = TableController::StoryTable();
		$params = array();
		$params['some1'] = "Test Work";
		$params['some2'] = "Yeah its work baby!";
		return $this->GetBuilder()->Prepare("main.html", $params);
	}
	
	public function Save($params)
	{
		$Story = TableController::StoryTable();
		$new_story = new stdClass();
		$new_story->TestField1 = $this->GetSafeString($_POST['Field1']);
		$new_story->TestField2 = false;
		$Story->Set($new_story);
		return $this->GetBuilder()->PrepareWithMerge("save.html", $content);
	}
	
}
?>
