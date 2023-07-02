import React from 'react';
import { ResponsiveRadar } from '@nivo/radar'


const NivoRadar = ({data}) => {
    return (
        <ResponsiveRadar
            data={data}
            keys={['valeur ca']}
            indexBy="label"
            valueFormat=">-.2f"
            margin={{ top: 50, right: 50, bottom: 40, left: 50 }}
            borderColor={{ from: 'color' }}
            gridLabelOffset={9}
            dotSize={10}
            dotColor={{ theme: 'background' }}
            dotBorderWidth={2}
            colors={{ scheme: 'nivo' }}
            blendMode="multiply"
            motionConfig="wobbly"
            legends={[
                {
                    anchor: 'top-left',
                    direction: 'column',
                    translateX: -20,
                    translateY: -30,
                    itemWidth: 90,
                    itemHeight: 20,
                    itemTextColor: '#999',
                    symbolSize: 17,
                    symbolShape: 'circle',
                    effects: [
                        {
                            on: 'hover',
                            style: {
                                itemTextColor: '#000'
                            }
                        }
                    ]
                }
            ]}
        />
    );
};

export default NivoRadar;