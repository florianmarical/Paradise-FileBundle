<?php

namespace Paradise\FileBundle\Controller;

use Paradise\FileBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * File controller.
 *
 */
class FileController extends Controller
{
    /**
     * Lists all file entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $files = $em->getRepository('PortailFileBundle:File')->findAll();

        return $this->render('file/index.html.twig', array(
            'files' => $files,
        ));
    }

    /**
     * Creates a new file entity.
     *
     */
    public function newAction(Request $request)
    {
        $file = new File();
        $media = $request->files->get('file');
        
        $file->setFile($media);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($file);

        $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
        $listener = $uploadableManager->getUploadableListener();
        $listener->setDefaultPath($this->getParameter('poi')['poi_image_directory']);
        $uploadableManager->markEntityToUpload($file, $file->getFile());

        $em->flush($file);
        return new JsonResponse(array('success' => true));
    }

    /**
     * Finds and displays a file entity.
     *
     */
    public function showAction(File $file)
    {
        $deleteForm = $this->createDeleteForm($file);

        return $this->render('file/show.html.twig', array(
            'file' => $file,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing file entity.
     *
     */
    public function editAction(Request $request, File $file)
    {
        $deleteForm = $this->createDeleteForm($file);
        $editForm = $this->createForm('Paradise\FileBundle\Form\FileType', $file);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_media_file_edit', array('id' => $file->getId()));
        }

        return $this->render('file/edit.html.twig', array(
            'file' => $file,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a file entity.
     *
     */
    public function deleteAction(Request $request, File $file)
    {
        $form = $this->createDeleteForm($file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush($file);
        }

        return $this->redirectToRoute('admin_media_file_index');
    }

    /**
     * Creates a form to delete a file entity.
     *
     * @param File $file The file entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(File $file)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_media_file_delete', array('id' => $file->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
