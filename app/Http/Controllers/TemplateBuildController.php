<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateBuildController extends Controller
{
    /**
     * Display all Template Builds for the user
     */
    public function index()
    {
        return redirect()->route('template.index');
    }

    /**
     * Display editor for the Template Build
     *
     * // TODO: Add Template Build object binding to route
     */
    public function showEditor(Request $request)
    {
        return view('template-builder.editor');
    }

    public function getImage(Request $request)
    {
        $size = null;
        if ($request->has('params')) {
            $size = str_replace(',', 'x', $request->get('params'));
        }

        if ($request->has('method') && $request->get('method') == 'placeholder') {
            return response(file_get_contents('https://placehold.it/' . $size))
                ->header('Content-Type', 'image/png');
        }

        if ($request->has('src')) {
            if (Str::contains($request->get('src'), '/storage/template-data/')) {
                $array = explode('/', $request->get('src'));
                $image = array_pop($array);
                $extensionParts = explode('.', $image);
                $extension = array_pop($extensionParts);
                $image = \Storage::get('email-images/' . $image);
                return response($image)->header('Content-Type', 'image/' . $extension);
            }
        }
    }

    public function getEdresFile(Request $request, $template, $file)
    {
        return file_get_contents(public_path() . "/templates/$template/edres/$file");
    }

    /**
     *  Some glue to keep the JS framework happy
     */
    public function getTemplate(Request $request, $template, $templateName)
    {
        return file_get_contents(public_path() . "/templates/$template/$templateName");
    }

    /**
     *  Some glue to keep the JS framework happy
     */
    public function getImageList(Request $request)
    {
        $files = \Storage::files('email-images', 'media');
        $list = [];

        foreach ($files as $file) {
            $data = new \stdClass();
            /* USE IF SLASHES ARE OKAY FOR FILENAMES
            $nameArray = explode('/', $file);
            $data->name = array_pop($nameArray);
             */
            $data->name = $file;
            $data->size = \Storage::size($file);
            $data->url = url(\Storage::url($file));

            $list[] = [
                'name' => $data->name,
                'size' => $data->size,
                'url' => $data->url,
            ];
        }

        return json_encode(['files' => $list], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Perform the upload of the image
     *
     * This will create a web-served copy of the image and a thumbnail
     *
     * @param Illuminate\Http\Request
     *
     * @return json
     */
    public function uploadImage(Request $request)
    {
        $files = $request->file('files');

        if (empty($files)) {
            return json_encode(['files' => []], JSON_UNESCAPED_SLASHES);
        }

        $attrs = [];
        foreach ($files as $file) {
            // dd($file->store('email-images', 'media'));
            $name = $file->store('email-images', 'media');
            \Storage::disk('media')->setVisibility($name, 'public');
            $size = \Storage::disk('media')->size($name);
            $url = url(\Storage::disk('media')->url($name));

            $attr = [
                'deleteType' => 'DELETE',
                'deleteUrl' => '/template-builder/delete-img/$file',
                'name' => $name,
                'size' => $size,
                'thumbnailUrl' => $url,
                'type' => 'image/png',
                'url' => $url,
            ];

            $attrs[] = $attr;
        }

        return json_encode(['files' => $attrs], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Download functionality
     *
     * @params Illuminate\Http\Request
     */
    public function download(Request $request)
    {
        return response($request->html)
            ->header('Content-Type', 'application/force-download')
            ->header('Content-Disposition', "attachment; filename='{$request->filename}'")
            ->header('Content-Length', strlen($request->html));
    }

    /**
     * Create a template from the builder form
     *
     * @param Illuminate\Http\Request
     */
    public function createTemplate(Request $request)
    {
        if (!$request->has('html')) {
            abort(401);
        }

        session(['email_html' => $request->get('html')]);

        return session('email_html');
    }
}
