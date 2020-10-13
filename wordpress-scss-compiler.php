<?php
use ScssPhp\ScssPhp\Compiler;



class Theme_SASS_Compiler {
	static function init($in, $out) {
		$self = new self();
		$cache_dir = dirname(__FILE__).'/scssphp/cache';
		$method =  'ScssPhp\ScssPhp\Formatter\Compressed';
		$sourcemaps = 'SOURCE_MAP_NONE';
		
		$file_in = $self->scss_file_info($in);
		$file_in_dir = get_template_directory().$file_in['dirname'];
		$file_in_name = $file_in['basename'];
		
		$file_out = $self->scss_file_info($out);
		$file_out_dir = get_template_directory().$file_out['dirname'];
		$file_out_name = $file_out['basename'];
		
		$in = get_template_directory().$in;
		$out = get_template_directory().$out;
		
		$permission = $self->scss_check_files_date($in, $out);
		
		if(!is_admin() && $permission) {
			include_once dirname(__FILE__).'/scssphp/scss.inc.php';
			$scss = new Compiler();
			$scss->setFormatter($method);
			$scss->setSourceMap($sourcemaps);
			$scss->setImportPaths($file_in_dir);
			$compiled = $scss->compile('@import "'.$file_in_name.'";');
			try {
				file_put_contents($file_out_dir.'/'.$file_out_name, $compiled);
				echo '<div class="alert alert-success" role="alert">sass_compiled</div>';
			}
			catch (Exception $e) {
				echo '<div class="alert alert-danger" role="alert">',  $e->getMessage(), "</div>";
			}
		}
	}
	
	public function scss_file_info($in) {
        $file_parts = pathinfo($in);
        if(!isset($file_parts['filename'])) {
            $file_parts['filename'] = substr($file_parts['basename'], 0, strrpos($file_parts['basename'], '.'));
        }
        return $file_parts;
    }
	
	public function scss_check_files_date($in, $out) {
		$latest_scss = 0;
		$latest_css = 0;
		if(!file_exists($out)) return true;
			
		$file_time = filemtime($in);
		if((int)$file_time > $latest_scss) {
			$latest_scss = $file_time;
		}
		$file_time = filemtime($out);
		if((int)$file_time > $latest_css) {
			$latest_css = $file_time;
		}
		if($latest_scss > $latest_css) {
			return true;
		}
		else {
			return false;
		}
	}
}