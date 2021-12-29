<?php
namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model {

    protected $table = 'blog';
    protected $primaryKey = 'id';

    protected $fillable = ['title', 'summary', 'content', 'status', 'seo_title', 'seo_keywords', 'seo_description', 'thumbnail'];

	public static function getExcerpt( $content, $length = 40, $more = '...' ) {
		$excerpt = strip_tags( trim( $content ) );
		$words = str_word_count( $excerpt, 2 );
		if ( count( $words ) > $length ) {
			$words = array_slice( $words, 0, $length, true );
			end( $words );
			$position = key( $words ) + strlen( current( $words ) );
			$excerpt = substr( $excerpt, 0, $position ) . $more;
		}
		return $excerpt;
	}


	public function categories(){
		return $this->hasMany('App\BlogCategoriesAssign');
	}



}
