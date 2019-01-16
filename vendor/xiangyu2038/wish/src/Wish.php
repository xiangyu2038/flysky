<?php
namespace XiangYu2038\Wish;
  class Wish
  {

      protected $with;
      protected $delete = [];
      protected $current = '';
      protected  $wish = [] ;
      protected $current_realation = '';

      public function with($with){

          $this -> with = clone $with;
          return $this;
      }



      public function wish($wish){
          $this ->wish[$wish]['add'] = [];
          $this ->wish[$wish]['only'] = [];
          $this ->wish[$wish]['except'] = [];
          $this ->wish[$wish]['delete'] = [];
          $this ->current = $wish;
          return $this;
      }

      public function add($add){
          if(!$this -> wish){
              $this ->wish('self');
          }

          $this->wish[$this ->current]['add'] = array_merge($this->wish[$this ->current]['add'],func_get_args());
          return $this;
      }
      public function except(){
          if(!$this -> wish){
              $this ->wish('self');
          }
          $this->wish[$this ->current]['except'] = array_merge($this->wish[$this ->current]['except'],func_get_args());
          return $this;
      }

      public function only(){
          if(!$this -> wish){
              $this ->wish('self');
          }
          $this->wish[$this ->current]['only'] = array_merge($this->wish[$this ->current]['only'],func_get_args());
          return $this;
      }

      public function get(){

          if($this->with instanceof \Illuminate\Database\Eloquent\Model){

              $this -> relation($this -> with);
              $this -> current_realation = 'self';//当前操作的关系

              $this -> self($this->with);///首先对本身进行处理

              return $this -> with;
          }

          foreach ($this->with as $v){
              $this -> relation($v);
              $this -> current_realation = 'self';//当前操作的关系
              $this -> self($v);///首先对本身进行处理

          }

          return $this->with;
      }

      public function setAdd($model,$add){

          foreach ($add as $v){
             $f = 'get'.ucfirst(convertUnderline($v));
              $model->setAttribute($v,$model->$f());
          }

      }
      public function setOnly($model,$only){

         $attributes = $model -> getAttributes();
          if($only){
              $model->setRawAttributes([]);///清空
          }
          foreach ($only as $v){
              $model->setAttribute($v,$attributes[$v]);
          }

      }

      public function setExcept($model,$except){

          foreach ($except as $v){

              unset($model->$v);
          }
      }

      public function self($model){

          if(isset($this -> wish['self'])){
             $this -> setAll($model,'self','self');
          }

      }

      public function relation($model){

          foreach ( $model -> getrelations() as $key=> $v){

                  $current_realation = $key;

                  if ($v instanceof \Illuminate\Database\Eloquent\Model) {

                      $this->relation($v);

                      $this->setAll($v, $key,$current_realation);

                      continue;
                  }

                  foreach ($v as $vv) {

                      $this->relation($vv);
                      $this->setAll($vv, $key,$current_realation);


              }
          }
      }

      public function setAll($collect,$wish,$current_realation){
///顺序很重要
          if ($collect instanceof \Illuminate\Database\Eloquent\Model) {
              $this -> setAllWithModel($collect,$wish,$current_realation);
          }else{
              foreach ($collect as $v){
                  $this -> setAllWithModel($v,$wish,$current_realation);
              }
          }

      }
      public function setAdds($model,$add){

          if($add instanceof \Illuminate\Database\Eloquent\Model){
              foreach ($add->toArray() as $key => $v){
                  $model->setAttribute($key,$v);
              }

          }else{
          foreach ($add as $v){
              $this -> setAdds($model,$v);
              }
          }


      }


      public function delete($realation,$flag=false){

          if(!$this -> wish){
             $this ->wish('self');
          }

          $arg = [$realation,$flag];
          array_push($this->wish[$this ->current]['delete'],$arg);
          return $this;

      }
      public function setDelete($model,$current_realation){


          if(isset($this -> wish[$current_realation])){
              foreach ($this -> wish[$current_realation]['delete'] as $v){
                  $re = $v[0];
                  if($v[1]){
                      ///如果全部删除
                      unset($model->$re);
                  }else{
                      //////部分删除

                      $value = $model -> $re;
                      $this -> setAdds($model,$value);
                      unset($model->$re);
                  }
              }
          }

      }
      public function setAllWithModel($model,$wish,$current_realation){

          if(isset($this -> wish[$wish])){
            $this -> setAdd($model,$this -> wish[$wish]['add']);
              $this -> setDelete($model,$current_realation);
              $this -> setExcept($model,$this -> wish[$wish]['except']);
              $this -> setOnly($model,$this -> wish[$wish]['only']);
          }

      }

  }