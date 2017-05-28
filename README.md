# Paradise FileBundle


### Installing

A step by step series of examples that tell you have to get a development env running

Composer.json

```
"require": {
       ...,
        "paradise/file-bundle": "dev-master"
    },
```

config.yml

```
twig:
    form_themes:
        - 'FileBundle:Form:fields.html.twig'
```


## Uses

In an entity

```
    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="Paradise\FileBundle\Entity\File", cascade={"persist", "remove"})
      @ORM\JoinColumn(name="favicon")
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png"},
     *     mimeTypesMessage = "The selected file is not a valid file",
     *     notFoundMessage = "The file was not found on the disk",
     *     uploadErrorMessage = "Error in upload file"
     * )
     */
    private $favicon;
```


In formType

```
$builder->add('favicon', FileUploadType::class, array('attr' => array('placeholder' => 'Favicon'), 'required' => false, 'path' => true));
```

If path is true, the picture appears in the render