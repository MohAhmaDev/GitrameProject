import * as React from 'react';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';

// function createData(calcule_Agregats, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation) {
//   return { calcule_Agregats, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation };
// }

// const rows = [
//   createData("Chiffre d'Affaires", null, null, 24, 4.0),
//   createData("Production de la période", 237, 9.0, 37, 4.3),
//   createData("Consommations de la période", 262, 16.0, 24, 6.0),
//   createData("Valeur ajoutée", 305, 3.7, 67, 4.3),
//   createData("EBE", 356, 16.0, 49, 3.9),
// ];

export default function DenseTable({data, id}) {
  return (
    <TableContainer component={Paper} id={id}>
      <Table sx={{ minWidth: 650 }} size="small" aria-label="a dense table">
        <TableHead>
          <TableRow>
            <TableCell>Calcule Agrégats</TableCell>
            <TableCell align="right">Montant_Realisation&nbsp;($)</TableCell>
            <TableCell align="right">Montant_Privision&nbsp;($)</TableCell>
            <TableCell align="right">Ecart_Valeur&nbsp;($)</TableCell>
            <TableCell align="right">taux_Realisation&nbsp;(%)</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {data.map((row) => (
            <TableRow
              key={row.calcule_Agregats}
              sx={{ '&:last-child td, &:last-child th': { border: 0 }, 'tr:nth-of-type(2)': {background: "blue"} }}
            >
              <TableCell component="th" scope="row">
                {row.calcule_Agregats}
              </TableCell>
              <TableCell align="right">{row.Montant_Realisation}</TableCell>
              <TableCell align="right">{row.Montant_Privision}</TableCell>
              <TableCell align="right">{row.Ecart_Valeur}</TableCell>
              <TableCell align="right">{row.taux_Realisation}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  );
}