# Paragraphs-Component-Library
A WIP Drupal 10 module to allow the export of Paragraph Types into a self-contained submodule. 

## The Problem:
Lets say you have site that utilizes the Drupal Paragraphs module. You create Paragraph Types as 'self-contained' components to add to pages on the site. Now lets say we want to take one of these Paragraph Types and use it on another Drupal site. 

We would have to find all the configurations for it (Paragraph, Field, and Field Storage), find it's template and associated CSS/JS, and lastly any preprocess code needed to make the component work properly. Depending on the state of the codebase the process of migrating a Paragraph Type to another site could be tedious. 

## The Solution:
What if we bundle Paragraph Types into a module? This idea is not revolutionary, but the aim of this module is to make the process of bundling Paragraph Types into a module easier by allowing a user to export existing Paragraph Types through the GUI and via the functionality in this module, a submodule will be created for the Paragraph Type that does the following:

* Creates a new module directory within the Paragraphs Component Library module with a corresponding **paragraph_type.info.yml** file.
* Create a template directory and corresponding **paragraph--paragraph_type.html.twig** file.
* Create a **paragraph_type.module** file with scaffolding code to attach the custom template.
* Finds and compiles all the needed configurations for the Paragraph Type and adds them to a **/config/optional** directory within the module.
* Creates JS/CSS files which are attached via the template automatically.
  * A **paragraph_type.libraries.yml** is automatically created and generated via this process also.
 
All the tools needed to export an existing Paragraph Type into a module should be included in this base module.

## How to Export a Paragraph Type
Right now a form can be accessed at the **/admin/structure/paragraphs_type/{paragraph_type}/export** route which will allow the Paragraph Type to be exported.

## Notes
This is a minimum viable product version 0.0.1. and has not been fully tested.
