# NMRium

[NMRium](https://www.nmrium.org/) is an open-source NMR spectra processing tool. 


&emsp;&emsp;&emsp;<img alt="NMRium" src="https://www.nmrium.org/brand/nmrium-logo.svg" width="24%" />

It provides a bundle of handy features, such as:
* Open various vendors and open file formats (JCAMP-DX file, a zipped Bruker folder, or a JEOL file).
* It accepts 1D (FID and FT) and 2D spectra (FT only).
* Advanced peak picking detection for 1D and 2D NMR spectra. Also, it can generate the NMR string required for publication or patent.
* All the processing and assignment can be stored as a “.nmrium” file. This file contains the original data as well as all the processing that was applied on the spectrum. Assignments of the molecule are also saved in the file. Additionally, export in NMReData is possible as well.

## Demo

<iframe width="100%" height="600" src="https://nmriumdev.nmrxiv.org"/>

<br/><br/>

## Integrating NMRium in nmrXiv

Development: https://nmriumdev.nmrxiv.org <br/>
Production: https://nmrium.nmrxiv.org

Since **[nmrXiv](https://nmrxiv.org/)** supports open-source initiatives natively, NMRium is our preferred NMR spectra processing and analysis tool. NMRium is available as a standalone, React component that can be integrated into other applications.

To enable and ensure that the integration is framework independent, we wrapped NMRium in a React App and can now be integrated into any third-party application via an iframe, irrespective of the underlying javascript framework. This layer one solution (nmrium-react-wrapper) enables us to extend and expose the NMRium APIs and integrate other NMR tools seamlessly.

This nmrium-react-wrapper is released under MIT opensource license - [GitHub](https://github.com/NFDI4Chem/nmrium-react-wrapper)

Please find the installation instructions and other documentation on the github readme page.

## Documentation
NMRium provides rich documentation on their webiste. [Here is the link for more details](https://docs.nmrium.org/).

