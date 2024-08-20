<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $year
 * @property string|null $isbn
 * @property string|null $preview
 *
 * @property BookAuthor[] $bookAuthors
 */
class Book extends \yii\db\ActiveRecord
{
    
    public $image;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'year'], 'required'],
            [['description'], 'string'],
            [['year'], 'integer'],
            [['name', 'isbn', 'preview'], 'string', 'max' => 255],
            [['author_ids'], 'each', 'rule' => ['integer']],
            [['image'], 'safe'],
            [['image'], 'file', 'extensions'=>'jpg, gif, png'],
            [['image'], 'file', 'maxSize'=>'100000'],
        ];
    }    
    

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'year' => 'Year',
            'isbn' => 'Isbn',
            'preview' => 'Preview',
        ];
    }
    
    public function behaviors()
    {
        return [
            [
                'class' => \voskobovich\linker\LinkerBehavior::className(),
                'relations' => [
                    'author_ids' => 'authors',
                ],
            ],
        ];
    }
    
    public function afterSave($insert, $changedAttributes) {        
        if($insert){
            $this->sendNewBookSms($this->author_ids);
        }
        
        parent::afterSave($insert, $changedAttributes);
    }
    
    public function getAuthors()
    {
        return $this->hasMany(Author::className(),['id' => 'author_id'])->viaTable('{{%book_author}}',['book_id' => 'id']);
    }
    
    public function sendNewBookSms($author_ids){
        
        foreach ($author_ids as $author_id){
            $recipients = \yii\helpers\ArrayHelper::getColumn(Notify::find()->select('phone')->where(['author_id'=>$author_id])->asArray()->all(), 'phone'); 
            $res = Yii::$app->smspilot->send($recipients,'Новая книга у автора по подписке!');
            
            var_dump($res);
            
        }
        
    }
    
}
