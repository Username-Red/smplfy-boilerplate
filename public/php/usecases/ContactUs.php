<?php

namespace SMPLFY\boilerplate;

class ContactUs
{
    private ExampleRepository $exampleRepository;
    private ContactUsRepository $contactUsRepository;

    public function __construct( ExampleRepository $exampleRepository, ContactUsRepository $contactUsRepository ) {
        $this->exampleRepository = $exampleRepository;
        $this->ContactUsRepository = $contactUsRepository;
    }

    public function handle_contact_us_submission($form, $entry) {
        // Gets the contact us entity to perform CRUD operations with
        //$contactUsEntity = $this->contactUsRepository->get_one_by_id('id');
        // Gets a completely blank fresh copy of the contact us entity
        //$contactUsEntity = new ContactUsEntity();


        //Gets the contact us entity
        $contactUsEntity = new ContactUsEntity($entry);

        $exampleEntity = $this->exampleRepository->get_one_for_current_user();

        $exampleEntity->email = $contactUsEntity->email;

        $this->exampleRepository->update($exampleEntity);

    }

}