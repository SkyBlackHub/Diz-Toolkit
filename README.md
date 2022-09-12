# Diz.Toolkit
A set of useful generic functions and tools for PHP

All functions are implemented as static methods and separated into individual kits.
* Array - kit for arrays manipulating
* Client - kit for obtaining various data about the user
* File - kit for working with the file system
* Filter - kit for filtering (checking and cleaning) input data
* Format - kit for format input data
* Math - kit with additional mathematical functions
* Parse - kit for parsing data in text of various formats
* Path - kit for manipulating paths separated by slash (/)
* String - kit designed for text processing (single-byte version)
* Text - kit designed for text processing (multibyte version, only UTF-8 supported)
* URL - kit for manipulating URLs
* Windows - kit designed to cover peculiarities of the Windows OS

Tools are separated into the following categories:
* Iterators - a set of classes that inherit the \Iterator interface, used to iterate through some data and pass to the foreach statement
* Tools - a collection of different classes, with no defined common trait