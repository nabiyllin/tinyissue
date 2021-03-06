<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Http\Controllers\Administration;

use Illuminate\Http\Request;
use Tinyissue\Form\Tag as Form;
use Tinyissue\Http\Controllers\Controller;
use Tinyissue\Http\Requests\FormRequest;
use Tinyissue\Model\Tag;

/**
 * TagsController is the controller class for managing administration request related to tags.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class TagsController extends Controller
{
    /**
     * Tag index page (List current tags).
     *
     * @param Tag $tag
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Tag $tag)
    {
        return view('administration.tags.index', [
            'tags'     => $tag->getGroupTags(),
            'projects' => $this->getLoggedUser()->projects()->get(),
        ]);
    }

    /**
     * Add new tag page.
     *
     * @param Form $form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getNew(Form $form)
    {
        return view('administration.tags.new', [
            'form'     => $form,
            'projects' => $this->getLoggedUser()->projects()->get(),
        ]);
    }

    /**
     * To create new tag.
     *
     * @param Tag             $tag
     * @param FormRequest\Tag $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postNew(Tag $tag, FormRequest\Tag $request)
    {
        $tag->createTag($request->all());

        return redirect('administration/tags')->with('notice', trans('tinyissue.tag_added'));
    }

    /**
     * Edit an existing tag.
     *
     * @param Tag  $tag
     * @param Form $form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEdit(Tag $tag, Form $form)
    {
        return view('administration.tags.edit', [
            'tag'      => $tag,
            'form'     => $form,
            'projects' => $this->getLoggedUser()->projects()->get(),
        ]);
    }

    /**
     * To update tag details.
     *
     * @param Tag             $tag
     * @param FormRequest\Tag $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Tag $tag, FormRequest\Tag $request)
    {
        $tag->update($request->all());

        return redirect('administration/tags')->with('notice', trans('tinyissue.tag_updated'));
    }

    /**
     * Delete tag.
     * 
     * @param Tag $tag
     *
     * @return mixed
     */
    public function getDelete(Tag $tag)
    {
        $tag->delete();

        return redirect('administration/tags')
            ->with('notice', trans('tinyissue.tag_has_been_deleted'));
    }
}
