---
sidebar_position: 3
title: Structure Editor
---

# Structure Editor
**[nmrXiv](https://nmrxiv.org)** uses [OpenChemLib](https://github.com/Actelion/openchemlib) as its structure editor, which is presented to the user during the [submission](/docs/submission-guides/submission/upload.md) and, after submission, on the [study](/docs/submission-guides/data-model/study#edit) level, and on the [dataset](/docs/submission-guides/data-model/dataset#edit) level within NMRium as, in the Chemical structures section, the user can press **+** to open the editor. The editor allows drawing up structures from scratch, and it also accepts structures loaded from [SMILES](https://www.daylight.com/dayhtml/doc/theory/theory.smiles.html). Above the editor, there is a link to this documentation that you can always access by clicking `Need help?`.

Here we provide an overview of the tools available in the editor:

<table>
  <tr>
    <th>Tool image</th>
    <th>Usage</th>
    <th>Tool image</th>
    <th>Usage</th>
  </tr>
  <tr>
    <td><img src="/img/editor/trash.png" alt=""/></td>
    <td>Clean canvas: it deletes everything resulting in an empty canvas.</td>
    <td><img src="/img/editor/undo.png" alt=""/></td>
    <td>Undo: it undoes the last change made.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/star.png" alt=""/></td>
    <td>Tidy up canvas: it changes the coordinates of the molecules and the bonds to provide a better view of the structure in terms of bond lengths and angles.</td>
    <td><img src="/img/editor/rotate.png" alt=""/></td>
    <td>Zoom and Rotate: by clicking anywhere in the space and scrolling up and down, it enables zooming out and in, while scrolling right and left, it rotates clock-wise and counter-clock.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/laso.png" alt=""/></td>
    <td>Lasso select: it enables the lasso selection of atoms and bonds to apply bulk actions, such as with the eraser.</td>
    <td><img src="/img/editor/tool.png" alt=""/></td>
    <td>Mapping: This tool is only for reactions and not applicable in nmrXiv</td>
  </tr>
  <tr>
    <td><img src="/img/editor/question.png" alt=""/></td>
    <td>Stereo configuration unknown: it enables adding question marks on chiral centers with unknown stereo configuration.</td>
    <td><img src="/img/editor/abs.png" alt=""/></td>
    <td>Enantiomere: this tool extends to three tools: **Abs**, which indicates that the provided enantiomer is the one intended. **Or** indicates that the intended structure is either this one or the other enantiomer. **And** indicates that the intended structure is both this enantiomer and the other one, with equal quantities, which means having a racemic mixture.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/eraser.png" alt=""/></td>
    <td>Eraser: it deletes atoms and bonds. When applied to atoms, it deletes the adjacent bonds, while when applied to bonds, it only deletes the adjacent atoms having no more bonds. Bulk erasing can be done by selecting a set of atoms and bonds. Please note that erasing a double bond will result in the full deletion and not in reducing the bond to a single one.</td>
    <td><img src="/img/editor/text.png" alt=""/></td>
    <td>Text: not applicable in nmrXiv.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/bond.png" alt=""/></td>
    <td>When applied in empty space, it draws a bond between two carbon atoms, while when applied on an atom, it draws a bond between that atom and a carbon atom. Additionally, it can link two already existing atoms. It can also increase the bond order (e.g., single bond to double bond).</td>
    <td><img src="/img/editor/chain.png" alt=""/></td>
    <td>Chain: it enables drawing saturated aliphatic chains.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/wedge.png" alt=""/></td>
    <td>Wedge bond: it indicates that the chiral bond is above the ring. It can be used similarly to the bond tool, or to change the bond type.</td>
    <td><img src="/img/editor/hatch.png" alt=""/></td>
    <td>Hatched bond: it indicates that the chiral bond is below the ring. It can be used similarly to the bond tool, or to change the bond type.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/triangle.png" alt=""/></td>
    <td>Triangle: it enables drawing a triangle of carbons.</td>
    <td><img src="/img/editor/square.png" alt=""/></td>
    <td>Square: it enables drawing a square of carbons.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/pentagon.png" alt=""/></td>
    <td>Pentagon: it enables drawing a pentagon of carbons.</td>
    <td><img src="/img/editor/hexagon.png" alt=""/></td>
    <td>Hexagon: it enables drawing a hexagon of carbons.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/heptagon.png" alt=""/></td>
    <td>Heptagon: it enables drawing a heptagon of carbons.</td>
    <td><img src="/img/editor/benzene.png" alt=""/></td>
    <td>Benzene: it enables drawing a Benzene molecules.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/plus.png" alt=""/></td>
    <td>Charge plus: it increases the positive charge of atoms.</td>
    <td><img src="/img/editor/minus.png" alt=""/></td>
    <td>Charge minus: it decreases the positive charge of atoms (increases the negative one).</td>
  </tr>
  <tr>
    <td><img src="/img/editor/c.png" alt=""/></td>
    <td>Carbon atom: it creates a carbon atom, along with hydrogen atoms bonded to it, to reach the carbon's four bonds. So if clicked in space, it creates a Methane molecule. It can also replace an existing atom with a carbon one.</td>
    <td><img src="/img/editor/si.png" alt=""/></td>
    <td>Silicon atom: it creates a silicon atom, along with hydrogen atoms bonded to it to reach the silicon's four bonds. So if clicked in space, it creates a Silane molecule. It can also replace an existing atom with a silicon one.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/n.png" alt=""/></td>
    <td>Nitrogen atom: it creates a nitrogen atom, along with hydrogen atoms bonded to it to reach the nitrogen's three bonds. So if clicked in space, it creates an Ammonia molecule. It can also replace an existing atom with a nitrogen one.</td>
    <td><img src="/img/editor/p.png" alt=""/></td>
    <td>Phosphorus atom: it creates a phosphorus atom, along with hydrogen atoms bonded to it to reach the phosphorus' three bonds. So if clicked in space, it creates a Phosphine molecule. It can also replace an existing atom with a phosphorus one.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/o.png" alt=""/></td>
    <td>Oxygen atom: it creates an oxygen atom, along with hydrogen atoms bonded to it to reach the oxygen's two bonds. So if clicked in space, it creates a water molecule. It can also replace an existing atom with an oxygen one.</td>
    <td><img src="/img/editor/s.png" alt=""/></td>
    <td>Sulfur atom: it creates a sulfur atom along with hydrogen atoms bonded to it to reach the sulfur's two bonds. So if clicked in space, it creates a Hydrogen sulfide molecule. It can also replace an existing atom with a sulfur one.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/f.png" alt=""/></td>
    <td>Fluorine atom: it creates a fluorine atom, along with a hydrogen atom bonded to it, to reach the fluorine's one bond. So if clicked in space, it creates a hydrogen fluoride molecule. It can also replace an existing atom with a fluorine one.</td>
    <td><img src="/img/editor/cl.png" alt=""/></td>
    <td>Chlorine atom: it creates a chlorine atom, along with a hydrogen atom bonded to it, to reach the chlorine's one bond. So if clicked in space, it creates a hydrogen chloride molecule. It can also replace an existing atom with a chlorine one.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/br.png" alt=""/></td>
    <td>Bromine atom: it creates a bromine atom, along with a hydrogen atom bonded to it to reach the bromine's one bond. So if clicked in space, it creates a hydrogen bromide molecule. It can also replace an existing atom with a bromine one.</td>
    <td><img src="/img/editor/i.png" alt=""/></td>
    <td>Iodine atom: it creates an iodine atom, along with a hydrogen atom bonded to it, to reach the iodine one bond. So if clicked in space, it creates a hydrogen iodide molecule. It can also replace an existing atom with an iodine one.</td>
  </tr>
  <tr>
    <td><img src="/img/editor/h.png" alt=""/></td>
    <td>Hydrogen atom: it creates a hydrogen atom, along with a hydrogen atom bonded to it, to reach the hydrogen's one bond. So if clicked in space, it creates a hydrogen molecule. It can also replace an existing atom with a hydrogen one.</td>
    <td><img src="/img/editor/q.png" alt=""/></td>
    <td>Atom properties: when applied to an atom, it displays its properties, including the label, mass, valence, and radical state, where the user can edit all of these values. It is mostly useful for isotope and radicals</td>
  </tr>
</table>

